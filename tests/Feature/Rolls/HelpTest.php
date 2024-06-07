<?php

declare(strict_types=1);

namespace Tests\Feature\Rolls;

use App\Models\Campaign;
use App\Models\Channel;
use App\Models\ChatUser;
use App\Rolls\Help;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * @medium
 */
final class HelpTest extends TestCase
{
    /**
     * Test getting help in an unlinked channel for a registered user that has
     * no campaigns.
     * @group discord
     */
    public function testGetHelpUnlinkedChannelRegisteredUserNoCampaigns(): void
    {
        $username = Str::random(5);

        $channel = new Channel([
            'server_id' => Str::random(10),
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
     * @group irc
     */
    public function testGetHelpUnlinkedChannelRegisteredUserWithCampaigns(): void
    {
        $username = Str::random(5);

        $channel = new Channel([
            'channel_id' => Str::random(10),
            'server_id' => Str::random(10),
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
            // @phpstan-ignore-next-line
            'registered_by' => $chatUser->user->id,
        ]);
        /** @var Campaign */
        $campaignGmed = Campaign::factory()->create([
            // @phpstan-ignore-next-line
            'gm' => $chatUser->user->id,
        ]);

        $help = (new Help('campaign', $channel->user, $channel))->forIrc();
        self::assertStringContainsString(
            'Commands for unregistered channels',
            $help
        );
        self::assertStringContainsString('Your campaigns:', $help);
    }
}
