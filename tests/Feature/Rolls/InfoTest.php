<?php

declare(strict_types=1);

namespace Tests\Feature\Rolls;

use App\Enums\ChannelType;
use App\Events\DiscordMessageReceived;
use App\Events\IrcMessageReceived;
use App\Models\Campaign;
use App\Models\Channel;
use App\Models\ChatCharacter;
use App\Models\ChatUser;
use App\Models\Irc\User as IrcUser;
use App\Models\User;
use App\Rolls\Info;
use Discord\Discord;
use Discord\Parts\Channel\Channel as DiscordChannel;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Jerodev\PhpIrcClient\IrcChannel;
use Jerodev\PhpIrcClient\IrcClient;
use Jerodev\PhpIrcClient\IrcConnection;
use Modules\Shadowrun5e\Models\Character;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use const PHP_EOL;

#[Medium]
final class InfoTest extends TestCase
{
    use WithFaker;

    /**
     * Test trying to get info for an unregistered Discord channel.
     */
    #[Group('discord')]
    public function testDiscordInfoUnregistered(): void
    {
        $messageMock = $this->createDiscordMessageMock('info');
        $event = new DiscordMessageReceived(
            $messageMock,
            self::createStub(Discord::class),
        );

        $username = Str::random(5);
        $channel = new Channel([
            'id' => $event->channel->id,
            'name' => $event->channel->name,
            'server_id' => $event->server->id,
            'type' => ChannelType::Discord,
        ]);
        $channel->user = $username;

        $expected = '**Debugging info**' . PHP_EOL
            . 'User Tag: ' . optional($event->user)->displayname . PHP_EOL
            . 'User ID: ' . optional($event->user)->id . PHP_EOL
            . 'Commlink User: Not linked' . PHP_EOL
            . 'Server Name: ' . $event->server->name . PHP_EOL
            . 'Server ID: ' . $event->server->id . PHP_EOL
            . 'Channel Name: ' . $event->channel->name . PHP_EOL
            . 'Channel ID: ' . $event->channel->id . PHP_EOL
            . 'System: Unregistered' . PHP_EOL
            . 'Character: No character' . PHP_EOL
            . 'Campaign: No campaign';

        $info = (new Info('info', $username, $channel, $event))->forDiscord();
        self::assertSame($expected, $info);
    }

    /**
     * Test trying to get info for a registered Discord channel without a
     * campaign.
     */
    #[Group('discord')]
    public function testDiscordInfoRegisteredNoCampaign(): void
    {
        $messageMock = $this->createDiscordMessageMock('info');

        /** @var DiscordChannel */
        $discordChannel = $messageMock->channel;

        $channel = Channel::factory()->create([
            'channel_id' => $discordChannel->id,
            'channel_name' => $discordChannel->name,
            'server_id' => optional($discordChannel->guild)->id,
            'server_name' => optional($discordChannel->guild)->name,
            'system' => 'shadowrun5e',
            'type' => ChannelType::Discord,
        ]);

        $event = new DiscordMessageReceived(
            $messageMock,
            self::createStub(Discord::class)
        );

        $expected = '**Debugging info**' . PHP_EOL
            . 'User Tag: ' . optional($event->user)->displayname . PHP_EOL
            . 'User ID: ' . optional($event->user)->id . PHP_EOL
            . 'Commlink User: Not linked' . PHP_EOL
            . 'Server Name: ' . $channel->server_name . PHP_EOL
            . 'Server ID: ' . $channel->server_id . PHP_EOL
            . 'Channel Name: ' . $channel->channel_name . PHP_EOL
            . 'Channel ID: ' . $channel->channel_id . PHP_EOL
            . 'System: Shadowrun 5th Edition' . PHP_EOL
            . 'Character: No character' . PHP_EOL
            . 'Campaign: No campaign';

        $info = (new Info('info', 'user', $channel, $event))->forDiscord();
        self::assertSame($expected, $info);
    }

    /**
     * Test a info roll in IRC for a registered channel with a campaign.
     */
    #[Group('irc')]
    public function testIrcInfoRegistered(): void
    {
        $server = $this->faker->domainName();
        $username = $this->faker->userName();

        $ircConnection = $this->createMock(IrcConnection::class);
        $ircConnection->expects(self::any())
            ->method('getServer')
            ->willReturn($server);
        $ircChannel = $this->createMock(IrcChannel::class);
        $ircChannel->expects(self::any())
            ->method('getName')
            ->willReturn('#commlink');
        $ircClient = $this->createMock(IrcClient::class);
        $ircClient->expects(self::any())
            ->method('getConnection')
            ->willReturn($ircConnection);

        $campaign = Campaign::factory()->create(['system' => 'shadowrun5e']);

        $channel = Channel::factory()->create([
            'campaign_id' => $campaign,
            'channel_id' => '#commlink',
            'channel_name' => '#commlink',
            'server_id' => $server . ':6667',
            'server_name' => $server,
            'system' => 'shadowrun5e',
            'type' => ChannelType::Irc,
        ]);
        $channel->user = $username;

        $user = User::factory()->create();

        $chatUser = ChatUser::factory()->create([
            'server_id' => $server . ':6667',
            'server_name' => $server,
            'server_type' => ChatUser::TYPE_IRC,
            'remote_user_id' => $username,
            'remote_user_name' => null,
            'user_id' => $user,
            'verified' => true,
        ]);

        $character = Character::factory()->create();

        ChatCharacter::factory()->create([
            'channel_id' => $channel,
            'character_id' => $character,
            'chat_user_id' => $chatUser,
        ]);

        $event = new IrcMessageReceived(
            message: ':roll info',
            user: new IrcUser(nick: $username),
            client: $ircClient,
            channel: $ircChannel,
        );

        $expected = 'Debugging info' . PHP_EOL
            . 'User name: ' . $username . PHP_EOL
            . 'Commlink User: ' . $user->email . PHP_EOL
            . 'Server: ' . $server . PHP_EOL
            . 'Channel name: #commlink' . PHP_EOL
            . 'System: Shadowrun 5th Edition' . PHP_EOL
            . 'Character: ' . $character . PHP_EOL
            . 'Campaign: ' . $campaign;

        $info = (new Info('info', $username, $channel, $event))->forIrc();
        self::assertSame($expected, $info);

        $character->delete();
    }

    /**
     * Test a info roll in IRC for a registered channel with a chat user but no
     * character.
     */
    #[Group('irc')]
    public function testIrcInfoRegisteredNoCharacter(): void
    {
        $server = $this->faker->domainName();
        $username = $this->faker->userName();

        $ircConnection = $this->createMock(IrcConnection::class);
        $ircConnection->expects(self::any())
            ->method('getServer')
            ->willReturn($server);
        $ircChannel = $this->createMock(IrcChannel::class);
        $ircChannel->expects(self::any())
            ->method('getName')
            ->willReturn('#commlink');
        $ircClient = $this->createMock(IrcClient::class);
        $ircClient->expects(self::any())
            ->method('getConnection')
            ->willReturn($ircConnection);

        $channel = Channel::factory()->create([
            'channel_id' => '#commlink',
            'channel_name' => '#commlink',
            'server_id' => $server . ':6667',
            'server_name' => $server,
            'system' => 'shadowrun5e',
            'type' => ChannelType::Irc,
        ]);
        $channel->user = $username;

        $user = User::factory()->create();

        ChatUser::factory()->create([
            'server_id' => $server . ':6667',
            'server_name' => $server,
            'server_type' => ChatUser::TYPE_IRC,
            'remote_user_id' => $username,
            'remote_user_name' => null,
            'user_id' => $user,
            'verified' => true,
        ]);

        $event = new IrcMessageReceived(
            message: ':roll info',
            user: new IrcUser(nick: $username),
            client: $ircClient,
            channel: $ircChannel,
        );

        $expected = 'Debugging info' . PHP_EOL
            . 'User name: ' . $username . PHP_EOL
            . 'Commlink User: ' . $user->email . PHP_EOL
            . 'Server: ' . $server . PHP_EOL
            . 'Channel name: #commlink' . PHP_EOL
            . 'System: Shadowrun 5th Edition' . PHP_EOL
            . 'Character: No character' . PHP_EOL
            . 'Campaign: No campaign';

        $info = (new Info('info', $username, $channel, $event))->forIrc();
        self::assertSame($expected, $info);
    }

    /**
     * Test rolling info in a Slack channel that somehow has an invalid
     * character ID linked.
     */
    #[Group('slack')]
    public function testSlackRegisteredWithInvalidCharacter(): void
    {
        $user = User::factory()->create();

        /** @var Channel $channel */
        $channel = Channel::factory()->create([
            'type' => ChannelType::Slack,
        ]);
        $channel->user = 'U' . Str::random(10);

        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => $channel->type,
            'user_id' => $user->id,
            'verified' => true,
        ]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel,
            'character_id' => 'invalid',
            'chat_user_id' => $chatUser,
        ]);

        $expectedFields = [
            [
                'title' => 'Team ID',
                'value' => $channel->server_id,
                'short' => true,
            ],
            [
                'title' => 'Channel ID',
                'value' => $channel->channel_id,
                'short' => true,
            ],
            [
                'title' => 'User ID',
                'value' => $channel->user,
                'short' => true,
            ],
            [
                'title' => 'Commlink User',
                'value' => $user->email,
                'short' => true,
            ],
            [
                'title' => 'System',
                'value' => config('commlink.systems')[$channel->system],
                'short' => true,
            ],
            [
                'title' => 'Character',
                'value' => 'Invalid character',
                'short' => true,
            ],
            [
                'title' => 'Campaign',
                'value' => 'No campaign',
                'short' => true,
            ],
        ];

        $info = (new Info('info', $this->faker->userName(), $channel, null))
            ->forSlack()
            ->jsonSerialize();
        self::assertArrayHasKey('attachments', $info);
        self::assertSame(
            'Debugging Info',
            $info['attachments'][0]['title'],
        );
        // @phpstan-ignore offsetAccess.notFound
        self::assertEquals($expectedFields, $info['attachments'][0]['fields']);
    }
}
