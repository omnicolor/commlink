<?php

declare(strict_types=1);

namespace Tests\Feature\Rolls;

use App\Events\DiscordMessageReceived;
use App\Models\Channel;
use App\Rolls\Info;
use Discord\Discord;
use Discord\Parts\Channel\Channel as TextChannel;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * @medium
 */
final class InfoTest extends TestCase
{
    /**
     * Test trying to get info for an unregistered Discord channel.
     * @test
     */
    public function testDiscordInfoUnregistered(): void
    {
        $messageMock = $this->createDiscordMessageMock('info');
        $event = new DiscordMessageReceived(
            $messageMock,
            $this->createStub(Discord::class)
        );

        $username = Str::random(5);
        $channel = new Channel([
            'id' => $event->channel->id,
            // @phpstan-ignore-next-line
            'name' => $event->channel->name,
            'server_id' => $event->server->id,
            'type' => Channel::TYPE_DISCORD,
        ]);
        $channel->user = $username;

        $expected = '**Debugging info**' . \PHP_EOL
            . 'User Tag: ' . optional($event->user)->displayname . \PHP_EOL
            . 'User ID: ' . optional($event->user)->id . \PHP_EOL
            . 'Server Name: ' . \PHP_EOL
            . 'Server ID: ' . $event->server->id . \PHP_EOL
            // @phpstan-ignore-next-line
            . 'Channel Name: ' . $event->channel->name . \PHP_EOL
            . 'Channel ID: ' . $event->channel->id . \PHP_EOL
            . 'System: Unregistered' . \PHP_EOL
            . 'Character: No character' . \PHP_EOL
            . 'Campaign: No campaign';

        $info = (new Info('info', $username, $channel, $event))->forDiscord();
        self::assertSame($expected, $info);
    }

    /**
     * Test trying to get info for a registered Discord channel without a
     * campaign.
     * @test
     */
    public function testDiscordInfoRegisteredNoCampaign(): void
    {
        $messageMock = $this->createDiscordMessageMock('info');

        /** @var TextChannel */
        $textChannel = $messageMock->channel;

        /** @var Channel */
        $channel = Channel::factory()->create([
            'channel_id' => $textChannel->id,
            // @phpstan-ignore-next-line
            'channel_name' => $textChannel->name,
            'server_id' => optional($textChannel->guild)->id,
            'server_name' => optional($textChannel->guild)->name,
            'system' => 'shadowrun5e',
            'type' => Channel::TYPE_DISCORD,
        ]);

        $event = new DiscordMessageReceived(
            $messageMock,
            $this->createStub(Discord::class)
        );

        $expected = '**Debugging info**' . \PHP_EOL
            . 'User Tag: ' . optional($event->user)->displayname . \PHP_EOL
            . 'User ID: ' . optional($event->user)->id . \PHP_EOL
            . 'Server Name: ' . $channel->server_name . \PHP_EOL
            . 'Server ID: ' . $channel->server_id . \PHP_EOL
            . 'Channel Name: ' . $channel->channel_name . \PHP_EOL
            . 'Channel ID: ' . $channel->channel_id . \PHP_EOL
            . 'System: Shadowrun 5th Edition' . \PHP_EOL
            . 'Character: No character' . \PHP_EOL
            . 'Campaign: No campaign';

        $info = (new Info('info', 'user', $channel, $event))->forDiscord();
        self::assertSame($expected, $info);
    }

    /*
    public function testSlackRegisteredButNotLinked(): void
    {
        /** @var User *
        $user = User::factory()->create();

        /** @var Channel *
        $channel = Channel::factory()->create([
            'type' => Channel::TYPE_SLACK,
        ]);
        $channel->user = 'U' . Str::random(10);

        /** @var ChatUser *
        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => $channel->type,
            'user_id' => $user->id,
            'verified' => true,
        ]);

        $info = (new Info('info', 'user', $channel
    }
    */
}
