<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Responses\Shadowrun5e;

use App\Http\Responses\Shadowrun5e\HelpResponse;
use App\Models\Campaign;
use App\Models\Channel;
use App\Models\ChatUser;
use App\Models\User;

/**
 * Tests for getting help in a Shadowrun 5E channel.
 * @group slack
 * @small
 */
final class HelpResponseTest extends \Tests\TestCase
{
    /**
     * Test the response.
     * @test
     */
    public function testResponse(): void
    {
        $response = new HelpResponse(
            'help',
            HelpResponse::HTTP_OK,
            [],
            new Channel()
        );
        self::assertStringContainsString(
            '"title":"Commlink - Shadowrun 5th Edition"',
            (string)$response
        );
    }

    /**
     * Test the response with a channel that's not linked with a character.
     * @test
     */
    public function testResponseUnregistered(): void
    {
        $response = new HelpResponse(
            'help',
            HelpResponse::HTTP_OK,
            [],
            new Channel()
        );
        $response = \json_decode((string)$response);
        self::assertSame('ephemeral', $response->response_type);
        self::assertEquals(
            (object)[
                'title' => 'No linked character',
                'text' => \sprintf(
                    'It doesn\'t look like you\'ve linked a character here. If '
                        . 'you\'ve already built a character in <%s|Commlink>, '
                        . 'type `/roll link <characterId>` to connect your '
                        . 'character here.',
                    config('app.url')
                ),
                'color' => HelpResponse::COLOR_INFO,
            ],
            $response->attachments[1]
        );
    }

    /**
     * Test the response with a registered user on a channel that has no
     * campaign, but the user doesn't either.
     * @medium
     * @test
     */
    public function testResponseLinkedUserNoCampaigns(): void
    {
        /** @var User */
        $user = User::factory()->create([]);
        /** @var Channel */
        $channel = Channel::factory()->make([
            'type' => Channel::TYPE_SLACK,
        ]);
        $channel->user = 'U' . \Str::random(10);
        ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_SLACK,
            'user_id' => $user,
            'verified' => true,
        ]);
        $response = new HelpResponse(
            'help',
            HelpResponse::HTTP_OK,
            [],
            $channel
        );

        $response = \json_decode((string)$response);
        // There's no additional attachment for No Linked campaign.
        self::assertCount(2, $response->attachments);
    }

    /**
     * Test the response with a registered user on a registered channel that has
     * no campaign, and the user has some campaigns they can link.
     * @medium
     * @test
     */
    public function testResponseLinkedUserButNoCampaignChannelRegistered(): void
    {
        /** @var User */
        $user = User::factory()->create([]);
        /** @var Channel */
        $channel = Channel::factory()->make(['type' => Channel::TYPE_SLACK]);
        $channel->user = 'U' . \Str::random(10);
        /** @var Campaign */
        $campaign = Campaign::factory()->create([
            'gm' => $user,
            'system' => $channel->system,
        ]);
        ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_SLACK,
            'user_id' => $user,
            'verified' => true,
        ]);

        $expected = sprintf(
            'It doesn\'t look like you\'ve linked a campaign here. Type `/roll '
                . 'campaign <campaignId>` to connect your campaign here. Your '
                . 'campaigns:' . \PHP_EOL . '· %d - %s',
            $campaign->id,
            $campaign->name,
        );

        $response = new HelpResponse(
            'help',
            HelpResponse::HTTP_OK,
            [],
            $channel
        );
        $response = \json_decode((string)$response);
        // There's no additional attachment for No Linked campaign.
        self::assertCount(3, $response->attachments);
        self::assertSame($expected, $response->attachments[2]->text);
    }

    /**
     * Test the response with a registered user on a unregistered channel that
     * has no campaign, and the user has some campaigns they can link.
     * @medium
     * @test
     */
    public function testResponseLinkedUserButNoCampaignChannelUnregistered(): void
    {
        /** @var User */
        $user = User::factory()->create([]);
        $channel = new Channel([
            'channel_id' => 'G' . \Str::random(10),
            'server_id' => 'T' . \Str::random(10),
            'type' => Channel::TYPE_SLACK,
        ]);
        $channel->user = 'U' . \Str::random(10);
        /** @var Campaign */
        $campaign = Campaign::factory()->create(['gm' => $user]);
        ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_SLACK,
            'user_id' => $user,
            'verified' => true,
        ]);

        $expected = sprintf(
            'It doesn\'t look like you\'ve linked a campaign here. Type `/roll '
                . 'campaign <campaignId>` to connect your campaign here. Your '
                . 'campaigns:' . \PHP_EOL . '· %d - %s (%s)',
            $campaign->id,
            $campaign->name,
            $campaign->getSystem()
        );

        $response = new HelpResponse(
            'help',
            HelpResponse::HTTP_OK,
            [],
            $channel
        );
        $response = \json_decode((string)$response);
        // There's no additional attachment for No Linked campaign.
        self::assertCount(3, $response->attachments);
        self::assertSame($expected, $response->attachments[2]->text);
    }
}
