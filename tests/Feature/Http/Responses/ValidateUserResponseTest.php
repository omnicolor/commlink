<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Responses;

use App\Exceptions\SlackException;
use App\Http\Responses\ValidateUserResponse;
use App\Models\Channel;
use App\Models\ChatUser;
use App\Models\User;

/**
 * Tests for validating a user from Slack.
 * @group slack
 */
final class ValidateUserResponseTest extends \Tests\TestCase
{
    /**
     * Test not having a channel.
     * @test
     */
    public function testNoChannel(): void
    {
        self::expectException(SlackException::class);
        self::expectExceptionMessage('Channel is required');
        new ValidateUserResponse('', ValidateUserResponse::HTTP_OK, []);
    }

    /**
     * Test trying to validate a user without sending the hash.
     * @test
     */
    public function testNoHash(): void
    {
        $channel = Channel::factory()->make();
        $channel->user = \Str::random(10);
        self::expectException(SlackException::class);
        self::expectExceptionMessage(sprintf(
            'To link your Commlink user, go to the <%s/settings|settings page> '
                . 'and copy the command listed there for this server. If the '
                . 'server isn\'t listed, follow the instructions there to add '
                . 'it. You\'ll need to know your server ID (`%s`) and your '
                . 'user ID (`%s`).',
            config('app.url'),
            $channel->server_id,
            $channel->user
        ));
        new ValidateUserResponse(
            'validate',
            ValidateUserResponse::HTTP_OK,
            [],
            $channel
        );
    }

    /**
     * Test trying to validate a user without a valid hash.
     * @test
     */
    public function testInvalidHash(): void
    {
        $user = User::factory()->create();

        $channel = Channel::factory()->make();
        $channel->user = \Str::random(10);

        // User that doesn't match the hash.
        $chatUser = ChatUser::factory()->create([
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_SLACK,
            'remote_user_id' => $channel->user,
            'user_id' => $user->id,
            'verified' => false,
        ]);

        self::expectException(SlackException::class);
        self::expectExceptionMessage(sprintf(
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
        new ValidateUserResponse(
            'validate aaa',
            ValidateUserResponse::HTTP_OK,
            [],
            $channel
        );
    }

    /**
     * Test trying to validate a user that has already been validated.
     * @test
     */
    public function testValidateAgain(): void
    {
        $user = User::factory()->create();
        $channel = Channel::factory()->make();
        $channel->user = \Str::random(10);
        $chatUser = ChatUser::factory()->create([
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_SLACK,
            'remote_user_id' => $channel->user,
            'user_id' => $user->id,
            'verified' => true,
        ]);
        self::expectException(SlackException::class);
        self::expectExceptionMessage('It looks like you\'re already verfied!');
        new ValidateUserResponse(
            sprintf('validate %s', $chatUser->verification),
            ValidateUserResponse::HTTP_OK,
            [],
            $channel
        );
    }

    /**
     * Test validating an unvalidated user in an unregistered channel.
     * @test
     */
    public function testValidateUnregistered(): void
    {
        $user = User::factory()->create();
        $channel = Channel::factory()->make();
        $channel->user = \Str::random(10);
        $chatUser = ChatUser::factory()->create([
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_SLACK,
            'remote_user_id' => $channel->user,
            'user_id' => $user->id,
            'verified' => false,
        ]);
        $response = new ValidateUserResponse(
            sprintf('validate %s', $chatUser->verification),
            ValidateUserResponse::HTTP_OK,
            [],
            $channel
        );
        self::assertStringContainsString(
            '/roll register <systemID>',
            (string)$response
        );
        self::assertStringNotContainsString(
            '/roll link <characterID>',
            (string)$response
        );
    }

    /**
     * Test validating an unvalidated user in an registered channel.
     * @test
     */
    public function testValidateRegistered(): void
    {
        $user = User::factory()->create();
        $channel = Channel::factory()->create();
        $channel->user = \Str::random(10);
        $chatUser = ChatUser::factory()->create([
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_SLACK,
            'remote_user_id' => $channel->user,
            'user_id' => $user->id,
            'verified' => false,
        ]);
        $response = new ValidateUserResponse(
            sprintf('validate %s', $chatUser->verification),
            ValidateUserResponse::HTTP_OK,
            [],
            $channel
        );
        self::assertStringNotContainsString(
            '/roll register <systemID>',
            (string)$response
        );
        self::assertStringContainsString(
            '/roll link <characterID>',
            (string)$response
        );
    }
}
