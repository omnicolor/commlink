<?php

declare(strict_types=1);

namespace Tests\Feature\Events;

use App\Events\IrcMessageReceived;
use App\Models\Irc\User;
use Jerodev\PhpIrcClient\IrcChannel;
use Jerodev\PhpIrcClient\IrcClient;
use Tests\TestCase;

/**
 * @group irc
 * @group events
 * @small
 */
final class IrcMessageReceivedTest extends TestCase
{
    /**
     * Test the constructor.
     * @test
     */
    public function testConstructor(): void
    {
        $clientStub = self::createStub(IrcClient::class);
        $channelStub = self::createStub(IrcChannel::class);

        $event = new IrcMessageReceived(
            ':roll foo',
            new User(nick: 'username'),
            $clientStub,
            $channelStub,
        );
        self::assertSame('foo', $event->content);
    }
}
