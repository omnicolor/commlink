<?php

declare(strict_types=1);

namespace Tests\Feature\Events;

use App\Events\DiscordMessageReceived;
use Discord\Parts\Channel\Channel as TextChannel;
use Discord\Parts\Channel\Message;
use Discord\Parts\Guild\Guild;
use Discord\Parts\User\User;

/**
 * Tests for Discord message events.
 * @group discord
 * @group events
 * @small
 */
final class DiscordMessageReceivedTest extends \Tests\TestCase
{
    /**
     * Test the constructor.
     * @test
     */
    public function testConstructor(): void
    {
        $channelStub = $this->createStub(TextChannel::class);
        $channelStub->method('__get')
            ->willReturn($this->createStub(Guild::class));

        $map = [
            ['author', $this->createStub(User::class)],
            ['channel', $channelStub],
            ['content', '/roll foo'],
        ];
        $messageStub = $this->createStub(Message::class);
        $messageStub->method('__get')->willReturnMap($map);

        $event = new DiscordMessageReceived($messageStub);
        self::assertSame('foo', $event->content);
    }
}
