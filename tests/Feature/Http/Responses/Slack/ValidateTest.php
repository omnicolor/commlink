<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Responses\Slack;

use App\Exceptions\SlackException;
use App\Http\Responses\Slack\ValidateResponse;
use App\Models\Campaign;
use App\Models\Channel;
use App\Models\Character;
use App\Models\ChatUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests for validating a user from Slack.
 * @group slack
 * @medium
 */
final class ValidateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test not having a channel.
     * @test
     */
    public function testNoChannel(): void
    {
        self::expectException(SlackException::class);
        self::expectExceptionMessage('Channel is required');
        new ValidateResponse();
    }

    /**
     * Test trying to validate a user without sending the hash.
     * @test
     */
    public function testNoHash(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make();
        $channel->user = \Str::random(10);
        self::expectException(SlackException::class);
        self::expectExceptionMessage(\sprintf(
            'To link your Commlink user, go to the <%s/settings|settings page> '
                . 'and copy the command listed there for this server. If the '
                . 'server isn\'t listed, follow the instructions there to add '
                . 'it. You\'ll need to know your server ID (`%s`) and your '
                . 'user ID (`%s`).',
            config('app.url'),
            $channel->server_id,
            $channel->user
        ));
        new ValidateResponse(
            content: 'validate',
            channel: $channel
        );
    }

    /**
     * Test trying to validate a user without a valid hash.
     * @test
     */
    public function testInvalidHash(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var Channel */
        $channel = Channel::factory()->make();
        $channel->user = \Str::random(10);

        // User that doesn't match the hash.
        /** @var ChatUser */
        $chatUser = ChatUser::factory()->create([
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_SLACK,
            'remote_user_id' => $channel->user,
            'user_id' => $user->id,
            'verified' => false,
        ]);

        self::expectException(SlackException::class);
        self::expectExceptionMessage(\sprintf(
            'We couldn\'t find a Commlink registration for this Slack team and '
                . 'your user. Go to the <%s/settings|settings page> and copy '
                . 'the command listed there for this server. If the server '
                . 'isn\'t listed, follow the instructions there to add it. '
                . 'You\'ll need to know your server ID (`%s`) and your user ID '
                . '(`%s`).',
            config('app.url'),
            $channel->server_id,
            $channel->user
        ));
        new ValidateResponse(
            content: 'validate aaa',
            channel: $channel,
        );
    }

    /**
     * Test trying to validate a user that has already been validated.
     * @test
     */
    public function testValidateAgain(): void
    {
        /** @var User */
        $user = User::factory()->create();
        /** @var Channel */
        $channel = Channel::factory()->make();
        $channel->user = \Str::random(10);
        /** @var ChatUser */
        $chatUser = ChatUser::factory()->create([
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_SLACK,
            'remote_user_id' => $channel->user,
            'user_id' => $user->id,
            'verified' => true,
        ]);
        self::expectException(SlackException::class);
        self::expectExceptionMessage('It looks like you\'re already verfied!');
        new ValidateResponse(
            content: \sprintf('validate %s', $chatUser->verification),
            channel: $channel,
        );
    }

    /**
     * Test validating an unvalidated user in an unregistered channel.
     * @test
     */
    public function testValidateUnregistered(): void
    {
        /** @var User */
        $user = User::factory()->create();
        /** @var Channel */
        $channel = Channel::factory()->make();
        $channel->user = \Str::random(10);
        /** @var ChatUser */
        $chatUser = ChatUser::factory()->create([
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_SLACK,
            'remote_user_id' => $channel->user,
            'user_id' => $user->id,
            'verified' => false,
        ]);
        $response = new ValidateResponse(
            content: \sprintf('validate %s', $chatUser->verification),
            channel: $channel
        );
        self::assertStringContainsString(
            '/roll register <system>',
            (string)$response
        );
        self::assertStringNotContainsString(
            '/roll link <characterId>',
            (string)$response
        );
    }

    /**
     * Test validating an unvalidated user in an registered channel.
     * @test
     */
    public function testValidateRegistered(): void
    {
        /** @var User */
        $user = User::factory()->create();
        /** @var Channel */
        $channel = Channel::factory()->create(['type' => Channel::TYPE_SLACK]);
        $channel->user = \Str::random(10);
        /** @var ChatUser */
        $chatUser = ChatUser::factory()->create([
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_SLACK,
            'remote_user_id' => $channel->user,
            'user_id' => $user->id,
            'verified' => false,
        ]);
        $response = new ValidateResponse(
            content: \sprintf('validate %s', $chatUser->verification),
            channel: $channel
        );
        self::assertStringNotContainsString(
            '/roll register <system>',
            (string)$response
        );
    }

    /**
     * Test validating an unvalidated user in an registered channel that has no
     * campaign if the user already has campaigns.
     * @test
     */
    public function testValidateRegisteredWithCampaign(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $channel = new Channel();
        $channel->server_id = 'G' . \Str::random(10);
        $channel->type = Channel::TYPE_SLACK;
        $channel->user = \Str::random(10);
        /** @var ChatUser */
        $chatUser = ChatUser::factory()->create([
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_SLACK,
            'remote_user_id' => $channel->user,
            'user_id' => $user->id,
            'verified' => false,
        ]);
        /** @var Campaign */
        $campaign = Campaign::factory()->create(['gm' => $user->id]);

        $expected = sprintf(
            '*Or*, you can type `\\/roll campaign <campaignId>` to register '
                . 'this channel for the campaign with ID <campaignId>. Your '
                . 'campaigns:\n\u00b7 %d - %s (%s)',
            $campaign->id,
            $campaign->name,
            $campaign->getSystem(),
        );

        $response = (string)(new ValidateResponse(
            content: \sprintf('validate %s', $chatUser->verification),
            channel: $channel
        ));
        self::assertStringContainsString($expected, $response);
    }

    /**
     * Test validating an unvalidated user in an registered channel, where the
     * user has a character appropriate to the system.
     * @test
     */
    public function testValidateRegisteredWithCharacters(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var Channel */
        $channel = Channel::factory()->create(['type' => Channel::TYPE_SLACK]);
        $channel->user = \Str::random(10);

        /** @var Character */
        $character = Character::factory()->create([
            'owner' => $user->email,
            'system' => $channel->system,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        /** @var ChatUser */
        $chatUser = ChatUser::factory()->create([
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_SLACK,
            'remote_user_id' => $channel->user,
            'user_id' => $user->id,
            'verified' => false,
        ]);
        $response = (string)(new ValidateResponse(
            content: \sprintf('validate %s', $chatUser->verification),
            channel: $channel
        ));
        self::assertStringContainsString(
            'Next, you can `\\/roll link <characterId>` to link a character '
                . 'to this channel, where <characterId> is one of:',
            $response
        );
        $character->delete();
    }
}
