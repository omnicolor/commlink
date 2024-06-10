<?php

declare(strict_types=1);

namespace Tests\Feature\Events;

use App\Events\IrcMessageReceived;
use App\Models\Irc\User;
use Jerodev\PhpIrcClient\IrcChannel;
use Jerodev\PhpIrcClient\IrcClient;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('events')]
#[Group('irc')]
#[Small]
final class IrcMessageReceivedTest extends TestCase
{
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
