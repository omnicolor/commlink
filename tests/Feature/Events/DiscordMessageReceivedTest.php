<?php

declare(strict_types=1);

namespace Tests\Feature\Events;

use App\Events\DiscordMessageReceived;
use CharlotteDunois\Yasmin\Models\Guild;
use CharlotteDunois\Yasmin\Models\Message;
use CharlotteDunois\Yasmin\Models\TextChannel;
use CharlotteDunois\Yasmin\Models\User;

/**
 * Tests for Discord message events.
 * @group discord
 * @group events
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
        $messageStub->method('__get')->will(self::returnValueMap($map));

        $event = new DiscordMessageReceived($messageStub);
        self::assertSame('foo', $event->content);
    }
}
