<?php

declare(strict_types=1);

namespace Tests\Feature\Rolls;

use App\Models\Campaign;
use App\Models\Channel;
use App\Models\ChatUser;
use App\Rolls\Help;

/**
 * @medium
 */
final class HelpTest extends \Tests\TestCase
{
    /**
     * Test getting help in an unlinked channel for a registered user that has
     * no campaigns.
     * @test
     */
    public function testGetHelpUnlinkedChannelRegisteredUserNoCampaigns(): void
    {
        $username = \Str::random(5);

        $channel = new Channel([
            'server_id' => \Str::random(10),
            'type' => Channel::TYPE_DISCORD,
        ]);
        $channel->user = $username;

        $chatUser = ChatUser::factory()->create([
            'server_id' => $channel->server_id,
            'server_type' => $channel->type,
            'remote_user_id' => $username,
            'verified' => true,
        ]);

        $help = (new Help('campaign', 'username', $channel))->forDiscord();
        self::assertStringContainsString(
            'Commands for unregistered channels',
            $help
        );
        self::assertStringNotContainsString('Your campaigns:', $help);
    }

    /**
     * Test getting help in an unlinked channel for a registered user with
     * campaigns.
     * @test
     */
    public function testGetHelpUnlinkedChannelRegisteredUserWithCampaigns(): void
    {
        $username = \Str::random(5);

        $channel = new Channel([
            'channel_id' => \Str::random(10),
            'server_id' => \Str::random(10),
            'type' => Channel::TYPE_DISCORD,
        ]);
        $channel->user = $username;

        /** @var ChatUser */
        $chatUser = ChatUser::factory()->create([
            'server_id' => $channel->server_id,
            'server_type' => $channel->type,
            'remote_user_id' => $username,
            'verified' => true,
        ]);

        /** @var Campaign */
        $campaignRegistered = Campaign::factory()->create([
            'registered_by' => $chatUser->user->id,
        ]);
        /** @var Campaign */
        $campaignGmed = Campaign::factory()->create([
            'gm' => $chatUser->user->id,
        ]);

        $help = (new Help('campaign', $channel->user, $channel))->forDiscord();
        self::assertStringContainsString(
            'Commands for unregistered channels',
            $help
        );
        self::assertStringContainsString('Your campaigns:', $help);
    }
}
