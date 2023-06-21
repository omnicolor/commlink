<?php

declare(strict_types=1);

namespace Tests\Feature\Listeners;

use App\Events\IrcMessageReceived;
use App\Listeners\HandleIrcMessage;
use App\Models\Campaign;
use App\Models\Channel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Jerodev\PhpIrcClient\IrcChannel;
use Jerodev\PhpIrcClient\IrcClient;
use Jerodev\PhpIrcClient\IrcConnection;
use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

/**
 * Test for IRC message event listener.
 * @group irc
 * @group events
 * @medium
 */
final class HandleIrcMessageTest extends TestCase
{
    use PHPMock;
    use RefreshDatabase;

    protected MockObject $randomInt;

    public function setUp(): void
    {
        parent::setUp();
        $this->randomInt = $this->getFunctionMock('App\\Rolls', 'random_int');
    }

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
                self::equalTo('@darkroach: That doesn\'t appear to be a valid command!')
            );
        $event = new IrcMessageReceived(
            message: ':roll foo',
            user: 'darkroach',
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
        $expected = 'Debugging info' . \PHP_EOL
            . 'User name: darkroach' . \PHP_EOL
            . 'Commlink User: Not linked' . \PHP_EOL
            . 'Server: chat.freenode.net' . \PHP_EOL
            . 'Channel name: #commlink' . \PHP_EOL
            . 'System: unregistered' . \PHP_EOL
            . 'Character: No character' . \PHP_EOL
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
            user: 'darkroach',
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

        $expected = 'Debugging info' . \PHP_EOL
            . 'User name: darkroach' . \PHP_EOL
            . 'Commlink User: Not linked' . \PHP_EOL
            . 'Server: chat.freenode.com' . \PHP_EOL
            . 'Channel name: #test-channel' . \PHP_EOL
            . 'System: Shadowrun 5th Edition' . \PHP_EOL
            . 'Character: No character' . \PHP_EOL
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
            user: 'darkroach',
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
        $this->randomInt->expects(self::exactly(2))->willReturn(3);

        $expected = 'darkroach rolled 6' . \PHP_EOL
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
            user: 'darkroach',
            client: $client,
            channel: $channel,
        );
        (new HandleIrcMessage())->handle($event);
    }
}
