<?php

declare(strict_types=1);

namespace Tests\Feature\Listeners;

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
use Tests\TestCase;

use const PHP_EOL;

/**
 * Test for IRC message event listener.
 * @group irc
 * @group events
 * @medium
 */
final class HandleIrcMessageTest extends TestCase
{
    use WithFaker;

    /**
     * Test a user trying to roll something invalid.
     * @test
     */
    public function testHandleInvalidComment(): void
    {
        $channel = $this->createMock(IrcChannel::class);
        $channel->expects(self::once())
            ->method('getName')
            ->willReturn('#commlink');
        $client = $this->createMock(IrcClient::class);
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
     * @test
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

        $channel = $this->createMock(IrcChannel::class);
        $channel->expects(self::any())
            ->method('getName')
            ->willReturn('#commlink');

        $connection = $this->createMock(IrcConnection::class);
        $connection->expects(self::any())
            ->method('getServer')
            ->willReturn('chat.freenode.net');

        $client = $this->createMock(IrcClient::class);
        $client->expects(self::any())
            ->method('getConnection')
            ->willReturn($connection);
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
     * @test
     */
    public function testHandleInfoRegisteredChannel(): void
    {
        /** @var Campaign */
        $campaign = Campaign::factory()->create([
            'system' => 'shadowrun5e',
        ]);

        /** @var Channel */
        $channel = Channel::factory()->create([
            'campaign_id' => $campaign,
            'channel_id' => '#test-channel',
            'server_id' => 'chat.freenode.com',
            'system' => 'shadowrun5e',
            'type' => Channel::TYPE_IRC,
        ]);

        $expected = 'Debugging info' . PHP_EOL
            . 'User name: darkroach' . PHP_EOL
            . 'Commlink User: Not linked' . PHP_EOL
            . 'Server: chat.freenode.com' . PHP_EOL
            . 'Channel name: #test-channel' . PHP_EOL
            . 'System: Shadowrun 5th Edition' . PHP_EOL
            . 'Character: No character' . PHP_EOL
            . 'Campaign: ' . $campaign->name;

        $ircChannel = $this->createMock(IrcChannel::class);
        $ircChannel->expects(self::any())
            ->method('getName')
            ->willReturn('#test-channel');

        $connection = $this->createMock(IrcConnection::class);
        $connection->expects(self::any())
            ->method('getServer')
            ->willReturn('chat.freenode.com');

        $client = $this->createMock(IrcClient::class);
        $client->expects(self::any())
            ->method('getConnection')
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
     * @test
     */
    public function testHandleGenericRoll(): void
    {
        DiceService::shouldReceive('rollMany')
            ->once()
            ->with(2, 6)
            ->andReturn([3, 3]);

        $expected = 'darkroach rolled 6' . PHP_EOL
            . 'Rolling: 2d6 = [3+3] = 6';

        $channel = $this->createMock(IrcChannel::class);
        $channel->expects(self::once())
            ->method('getName')
            ->willReturn('#commlink');
        $client = $this->createMock(IrcClient::class);
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
        $ircChannel = $this->createMock(IrcChannel::class);
        $ircChannel->expects(self::once())
            ->method('getName')
            ->willReturn('#commlink');
        $ircConnection = $this->createMock(IrcConnection::class);
        $ircConnection->expects(self::any())
            ->method('getServer')
            ->willReturn($server);
        $client = $this->createMock(IrcClient::class);
        $client->expects(self::once())
            ->method('say')
            ->with(
                self::equalTo('#commlink'),
                self::equalTo($expected)
            );
        $client->expects(self::any())
            ->method('getConnection')
            ->willReturn($ircConnection);

        Channel::factory()->create([
            'channel_id' => '#commlink',
            'server_id' => $server,
            'system' => 'shadowrun5e',
            'type' => Channel::TYPE_IRC,
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
        $ircChannel = $this->createMock(IrcChannel::class);
        $ircChannel->expects(self::once())
            ->method('getName')
            ->willReturn('#commlink');
        $ircConnection = $this->createMock(IrcConnection::class);
        $ircConnection->expects(self::any())
            ->method('getServer')
            ->willReturn($server);
        $client = $this->createMock(IrcClient::class);
        $client->expects(self::once())
            ->method('say')
            ->with(
                self::equalTo('#commlink'),
                self::equalTo($expected)
            );
        $client->expects(self::any())
            ->method('getConnection')
            ->willReturn($ircConnection);

        Channel::factory()->create([
            'channel_id' => '#commlink',
            'server_id' => $server,
            'system' => 'shadowrun5e',
            'type' => Channel::TYPE_IRC,
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
        $ircChannel = $this->createMock(IrcChannel::class);
        $ircChannel->expects(self::once())
            ->method('getName')
            ->willReturn('#commlink');
        $ircConnection = $this->createMock(IrcConnection::class);
        $ircConnection->expects(self::any())
            ->method('getServer')
            ->willReturn($server);
        $client = $this->createMock(IrcClient::class);
        $client->expects(self::once())->method('say');
        $client->expects(self::any())
            ->method('getConnection')
            ->willReturn($ircConnection);

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
            'type' => Channel::TYPE_IRC,
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
