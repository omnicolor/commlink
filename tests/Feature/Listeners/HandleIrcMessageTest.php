<?php

declare(strict_types=1);

namespace Tests\Feature\Listeners;

use App\Enums\ChannelType;
use App\Events\IrcMessageReceived;
use App\Events\RollEvent;
use App\Listeners\HandleIrcMessage;
use App\Models\Campaign;
use App\Models\Channel;
use App\Models\Irc\User;
use Facades\App\Services\DiceService;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Jerodev\PhpIrcClient\IrcChannel;
use Jerodev\PhpIrcClient\IrcClient;
use Jerodev\PhpIrcClient\IrcConnection;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use const PHP_EOL;

#[Group('irc')]
#[Group('events')]
#[Medium]
final class HandleIrcMessageTest extends TestCase
{
    use WithFaker;

    /**
     * Test a user trying to roll something invalid.
     */
    public function testHandleInvalidComment(): void
    {
        $channel = self::createStub(IrcChannel::class);
        $channel->method('getName')->willReturn('#commlink');
        $client = self::createMock(IrcClient::class);
        $client->expects(self::once())
            ->method('say')
            ->with(
                self::equalTo('#commlink'),
                self::equalTo('darkroach: That doesn\'t appear to be a valid command!')
            );
        $event = new IrcMessageReceived(
            message: ':roll foo',
            user: new User(nick: 'darkroach'),
            client: $client,
            channel: $channel,
        );
        (new HandleIrcMessage())->handle($event);
    }

    /**
     * Test rolling a non-dice, non-specific roll.
     */
    public function testHandleInfoRoll(): void
    {
        $expected = 'Debugging info' . PHP_EOL
            . 'User name: darkroach' . PHP_EOL
            . 'Commlink User: Not linked' . PHP_EOL
            . 'Server: chat.freenode.net' . PHP_EOL
            . 'Channel name: #commlink' . PHP_EOL
            . 'System: unregistered' . PHP_EOL
            . 'Character: No character' . PHP_EOL
            . 'Campaign: No campaign';

        $channel = self::createStub(IrcChannel::class);
        $channel->method('getName')->willReturn('#commlink');

        $connection = self::createStub(IrcConnection::class);
        $connection->method('getServer')->willReturn('chat.freenode.net');

        $client = self::createMock(IrcClient::class);
        $client->method('getConnection')->willReturn($connection);
        $client->expects(self::once())
            ->method('say')
            ->with(
                self::equalTo('#commlink'),
                self::equalTo($expected)
            );

        $event = new IrcMessageReceived(
            message: ':roll info',
            user: new User(nick: 'darkroach'),
            client: $client,
            channel: $channel,
        );
        (new HandleIrcMessage())->handle($event);
    }

    /**
     * Test getting info for a registered channel.
     */
    public function testHandleInfoRegisteredChannel(): void
    {
        $campaign = Campaign::factory()->create([
            'system' => 'shadowrun5e',
        ]);
        Channel::factory()->create([
            'campaign_id' => $campaign,
            'channel_id' => '#test-channel',
            'server_id' => 'chat.freenode.com',
            'system' => 'shadowrun5e',
            'type' => ChannelType::Irc,
        ]);

        $expected = 'Debugging info' . PHP_EOL
            . 'User name: darkroach' . PHP_EOL
            . 'Commlink User: Not linked' . PHP_EOL
            . 'Server: chat.freenode.com' . PHP_EOL
            . 'Channel name: #test-channel' . PHP_EOL
            . 'System: Shadowrun 5th Edition' . PHP_EOL
            . 'Character: No character' . PHP_EOL
            . 'Campaign: ' . $campaign->name;

        $ircChannel = self::createStub(IrcChannel::class);
        $ircChannel->method('getName')->willReturn('#test-channel');

        $connection = self::createStub(IrcConnection::class);
        $connection->method('getServer')->willReturn('chat.freenode.com');

        $client = self::createMock(IrcClient::class);
        $client->method('getConnection')
            ->willReturn($connection);
        $client->expects(self::once())
            ->method('say')
            ->with(
                self::equalTo('#test-channel'),
                self::equalTo($expected)
            );

        $event = new IrcMessageReceived(
            message: ':roll info',
            user: new User(nick: 'darkroach'),
            client: $client,
            channel: $ircChannel,
        );
        (new HandleIrcMessage())->handle($event);
    }

    /**
     * Test rolling a non-system specific roll.
     */
    public function testHandleGenericRoll(): void
    {
        DiceService::shouldReceive('rollMany')
            ->once()
            ->with(2, 6)
            ->andReturn([3, 3]);

        $expected = 'darkroach rolled 6' . PHP_EOL
            . 'Rolling: 2d6 = [3+3] = 6';

        $channel = self::createStub(IrcChannel::class);
        $channel->method('getName')->willReturn('#commlink');
        $client = self::createMock(IrcClient::class);
        $client->expects(self::once())
            ->method('say')
            ->with(
                self::equalTo('#commlink'),
                self::equalTo($expected)
            );

        $event = new IrcMessageReceived(
            message: ':roll 2d6',
            user: new User(nick: 'darkroach'),
            client: $client,
            channel: $channel,
        );
        (new HandleIrcMessage())->handle($event);
    }

    public function testHandleSystemSpecificNumberRoll(): void
    {
        DiceService::shouldReceive('rollOne')
            ->times(2)
            ->with(6)
            ->andReturn(6);

        $expected = 'darkroach rolled 2 dice' . PHP_EOL
            . 'Rolled 2 successes' . PHP_EOL
            . 'Rolls: 6 6';

        $server = $this->faker->domainName();
        $ircChannel = self::createStub(IrcChannel::class);
        $ircChannel->method('getName')->willReturn('#commlink');
        $ircConnection = self::createStub(IrcConnection::class);
        $ircConnection->method('getServer')->willReturn($server);
        $client = self::createMock(IrcClient::class);
        $client->expects(self::once())
            ->method('say')
            ->with(
                self::equalTo('#commlink'),
                self::equalTo($expected)
            );
        $client->method('getConnection')
            ->willReturn($ircConnection);

        Channel::factory()->create([
            'channel_id' => '#commlink',
            'server_id' => $server,
            'system' => 'shadowrun5e',
            'type' => ChannelType::Irc,
        ]);

        $event = new IrcMessageReceived(
            message: ':roll 2',
            user: new User(nick: 'darkroach'),
            client: $client,
            channel: $ircChannel,
        );
        (new HandleIrcMessage())->handle($event);
    }

    public function testHandleSystemSpecificHelpRoll(): void
    {
        Event::fake();

        $expected = 'Commlink - Shadowrun 5th Edition' . PHP_EOL
            . 'Commlink is a Slack/Discord bot that lets you roll Shadowrun '
            . '5E dice.' . PHP_EOL . '· `6 [text]` - Roll 6 dice, with '
            . 'optional text (automatics, perception, etc)' . PHP_EOL
            . '· `12 6 [text]` - Roll 12 dice with a limit of 6' . PHP_EOL
            . '· `XdY[+C] [text]` - Roll X dice with Y sides, optionally '
            . 'adding C to the result, optionally describing that the roll is '
            . 'for "text"' . PHP_EOL . PHP_EOL
            . 'Player' . PHP_EOL . 'No character linked' . PHP_EOL
            . '· `link <characterId>` - Link a character to this channel'
            . PHP_EOL . '· `init 12+3d6` - Roll your initiative' . PHP_EOL
            . PHP_EOL;
        $server = $this->faker->domainName();
        $ircChannel = self::createStub(IrcChannel::class);
        $ircChannel->method('getName')->willReturn('#commlink');
        $ircConnection = self::createStub(IrcConnection::class);
        $ircConnection->method('getServer')->willReturn($server);
        $client = self::createMock(IrcClient::class);
        $client->expects(self::once())
            ->method('say')
            ->with(
                self::equalTo('#commlink'),
                self::equalTo($expected)
            );
        $client->method('getConnection')->willReturn($ircConnection);

        Channel::factory()->create([
            'channel_id' => '#commlink',
            'server_id' => $server,
            'system' => 'shadowrun5e',
            'type' => ChannelType::Irc,
        ]);

        $event = new IrcMessageReceived(
            message: ':roll help',
            user: new User(nick: 'darkroach'),
            client: $client,
            channel: $ircChannel,
        );
        (new HandleIrcMessage())->handle($event);

        Event::assertNotDispatched(RollEvent::class);
    }

    public function testHandleSystemSpecificRollThatShouldBroadcast(): void
    {
        Event::fake();

        $server = $this->faker->domainName();
        $ircChannel = self::createStub(IrcChannel::class);
        $ircChannel->method('getName')->willReturn('#commlink');
        $ircConnection = self::createStub(IrcConnection::class);
        $ircConnection->method('getServer')->willReturn($server);
        $client = self::createStub(IrcClient::class);
        $client->method('say');
        $client->method('getConnection')->willReturn($ircConnection);

        $campaign = Campaign::factory()->create([
            'options' => [
                'nightCityTarot' => true,
            ],
            'system' => 'cyberpunkred',
        ]);
        Channel::factory()->create([
            'campaign_id' => $campaign,
            'channel_id' => '#commlink',
            'server_id' => $server,
            'system' => 'cyberpunkred',
            'type' => ChannelType::Irc,
        ]);

        $event = new IrcMessageReceived(
            message: ':roll tarot',
            user: new User(nick: 'darkroach'),
            client: $client,
            channel: $ircChannel,
        );
        (new HandleIrcMessage())->handle($event);

        Event::assertDispatched(RollEvent::class);
    }
}
