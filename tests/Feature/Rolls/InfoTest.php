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

use function config;

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
        $message_mock = $this->createDiscordMessageMock('info');
        $message_mock->expects(self::never())
            ->method('reply');
        $event = new DiscordMessageReceived(
            $message_mock,
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
            . 'User Tag: ' . $event->user?->displayname . PHP_EOL
            . 'User ID: ' . $event->user?->id . PHP_EOL
            . 'Commlink User: Not linked' . PHP_EOL
            . 'Server Name: ' . $event->server->name . PHP_EOL
            . 'Server ID: ' . $event->server->id . PHP_EOL
            . 'Channel Name: ' . $event->channel->name . PHP_EOL
            . 'Channel ID: ' . $event->channel->id . PHP_EOL
            . 'System: Unregistered' . PHP_EOL
            . 'Character: No character' . PHP_EOL
            . 'Campaign: No campaign';

        $info = (new Info('info', $username, $channel, $event))
            ->forDiscord();
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
        $messageMock->expects(self::never())
            ->method('reply');

        /** @var DiscordChannel $discord_channel */
        $discord_channel = $messageMock->channel;

        $channel = Channel::factory()->create([
            'channel_id' => $discord_channel->id,
            'channel_name' => $discord_channel->name,
            'server_id' => $discord_channel->guild?->id,
            'server_name' => $discord_channel->guild?->name,
            'system' => 'shadowrun5e',
            'type' => ChannelType::Discord,
        ]);

        $event = new DiscordMessageReceived(
            $messageMock,
            self::createStub(Discord::class)
        );

        $expected = '**Debugging info**' . PHP_EOL
            . 'User Tag: ' . $event->user?->displayname . PHP_EOL
            . 'User ID: ' . $event->user?->id . PHP_EOL
            . 'Commlink User: Not linked' . PHP_EOL
            . 'Server Name: ' . $channel->server_name . PHP_EOL
            . 'Server ID: ' . $channel->server_id . PHP_EOL
            . 'Channel Name: ' . $channel->channel_name . PHP_EOL
            . 'Channel ID: ' . $channel->channel_id . PHP_EOL
            . 'System: Shadowrun 5th Edition' . PHP_EOL
            . 'Character: No character' . PHP_EOL
            . 'Campaign: No campaign';

        $info = (new Info('info', 'user', $channel, $event))
            ->forDiscord();
        self::assertSame($expected, $info);
    }

    /**
     * Test an info roll in IRC for a registered channel with a campaign.
     */
    #[Group('irc')]
    public function testIrcInfoRegistered(): void
    {
        $server = $this->faker->domainName();
        $user_name = $this->faker->userName();

        $irc_connection = self::createStub(IrcConnection::class);
        $irc_connection->method('getServer')->willReturn($server);
        $irc_channel = self::createStub(IrcChannel::class);
        $irc_channel->method('getName')->willReturn('#commlink');
        $irc_client = self::createStub(IrcClient::class);
        $irc_client->method('getConnection')->willReturn($irc_connection);

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
        $channel->user = $user_name;

        $user = User::factory()->create();

        $chat_user = ChatUser::factory()->create([
            'server_id' => $server . ':6667',
            'server_name' => $server,
            'server_type' => ChatUser::TYPE_IRC,
            'remote_user_id' => $user_name,
            'remote_user_name' => null,
            'user_id' => $user,
            'verified' => true,
        ]);

        $character = Character::factory()->create();

        ChatCharacter::factory()->create([
            'channel_id' => $channel,
            'character_id' => $character,
            'chat_user_id' => $chat_user,
        ]);

        $event = new IrcMessageReceived(
            message: ':roll info',
            user: new IrcUser(nick: $user_name),
            client: $irc_client,
            channel: $irc_channel,
        );

        $expected = 'Debugging info' . PHP_EOL
            . 'User name: ' . $user_name . PHP_EOL
            . 'Commlink User: ' . $user->email . PHP_EOL
            . 'Server: ' . $server . PHP_EOL
            . 'Channel name: #commlink' . PHP_EOL
            . 'System: Shadowrun 5th Edition' . PHP_EOL
            . 'Character: ' . $character . PHP_EOL
            . 'Campaign: ' . $campaign;

        $info = (new Info('info', $user_name, $channel, $event))
            ->forIrc();
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
        $user_name = $this->faker->userName();

        $irc_connection = self::createStub(IrcConnection::class);
        $irc_connection->method('getServer')->willReturn($server);
        $irc_channel = self::createStub(IrcChannel::class);
        $irc_channel->method('getName')->willReturn('#commlink');
        $irc_client = self::createStub(IrcClient::class);
        $irc_client->method('getConnection')->willReturn($irc_connection);

        $channel = Channel::factory()->create([
            'channel_id' => '#commlink',
            'channel_name' => '#commlink',
            'server_id' => $server . ':6667',
            'server_name' => $server,
            'system' => 'shadowrun5e',
            'type' => ChannelType::Irc,
        ]);
        $channel->user = $user_name;

        $user = User::factory()->create();

        ChatUser::factory()->create([
            'server_id' => $server . ':6667',
            'server_name' => $server,
            'server_type' => ChatUser::TYPE_IRC,
            'remote_user_id' => $user_name,
            'remote_user_name' => null,
            'user_id' => $user,
            'verified' => true,
        ]);

        $event = new IrcMessageReceived(
            message: ':roll info',
            user: new IrcUser(nick: $user_name),
            client: $irc_client,
            channel: $irc_channel,
        );

        $expected = 'Debugging info' . PHP_EOL
            . 'User name: ' . $user_name . PHP_EOL
            . 'Commlink User: ' . $user->email . PHP_EOL
            . 'Server: ' . $server . PHP_EOL
            . 'Channel name: #commlink' . PHP_EOL
            . 'System: Shadowrun 5th Edition' . PHP_EOL
            . 'Character: No character' . PHP_EOL
            . 'Campaign: No campaign';

        $info = (new Info('info', $user_name, $channel, $event))
            ->forIrc();
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

        $channel = Channel::factory()->create([
            'type' => ChannelType::Slack,
        ]);
        $channel->user = 'U' . Str::random(10);

        $chat_user = ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => $channel->type,
            'user_id' => $user->id,
            'verified' => true,
        ]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel,
            'character_id' => 'invalid',
            'chat_user_id' => $chat_user,
        ]);

        $expected_fields = [
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

        $info = (new Info('info', $this->faker->userName(), $channel))
            ->forSlack()
            ->jsonSerialize();
        self::assertArrayHasKey('attachments', $info);
        self::assertSame(
            'Debugging Info',
            $info['attachments'][0]['title'],
        );
        // @phpstan-ignore offsetAccess.notFound
        self::assertEquals($expected_fields, $info['attachments'][0]['fields']);
    }
}
