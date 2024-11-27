<?php

declare(strict_types=1);

namespace Tests\Feature\Events;

use App\Events\DiscordMessageReceived;
use Discord\Discord;
use Discord\Parts\Channel\Channel as TextChannel;
use Discord\Parts\Channel\Message;
use Discord\Parts\Guild\Guild;
use Discord\Parts\Thread\Thread;
use Discord\Parts\User\User;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('discord')]
#[Group('events')]
#[Small]
final class DiscordMessageReceivedTest extends TestCase
{
    public function testConstructor(): void
    {
        $channelStub = self::createStub(TextChannel::class);
        $channelStub->method('__get')
            ->willReturn(self::createStub(Guild::class));

        $map = [
            ['author', self::createStub(User::class)],
            ['channel', $channelStub],
            ['content', '/roll foo'],
        ];
        $messageStub = self::createStub(Message::class);
        $messageStub->method('__get')->willReturnMap($map);

        $event = new DiscordMessageReceived(
            $messageStub,
            self::createStub(Discord::class)
        );
        self::assertSame('foo', $event->content);
    }

    public function testConstructorMessageHasNoChannel(): void
    {
        $messageStub = self::createStub(Message::class);
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Cannot handle null channels or servers');
        new DiscordMessageReceived(
            $messageStub,
            self::createStub(Discord::class)
        );
    }

    public function testConstructorMessageHasNoGuild(): void
    {
        $channelStub = self::createStub(TextChannel::class);
        $messageStub = self::createStub(Message::class);
        $messageStub->method('__get')->willReturn($channelStub);

        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Cannot handle null channels or servers');
        new DiscordMessageReceived(
            $messageStub,
            self::createStub(Discord::class)
        );
    }

    public function testConstructorMessageIsAThread(): void
    {
        $channelStub = self::createStub(Thread::class);
        $channelStub->method('__get')
            ->willReturn(self::createStub(Guild::class));

        $messageStub = self::createStub(Message::class);
        $messageStub->method('__get')->willReturn($channelStub);

        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Cannot handle threads');
        new DiscordMessageReceived(
            $messageStub,
            self::createStub(Discord::class)
        );
    }
}
