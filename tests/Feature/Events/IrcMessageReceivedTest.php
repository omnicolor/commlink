<?php

declare(strict_types=1);

namespace Tests\Feature\Events;

use App\Events\IrcMessageReceived;
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
        $event = new DiscordMessageReceived(
            ':roll foo',
            'username',
            $client,
            $channel,
        );
        self::assertSame('foo', $event->content);
    }
}
