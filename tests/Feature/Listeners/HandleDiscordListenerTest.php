<?php

declare(strict_types=1);

namespace Tests\Feature\Listeners;

use App\Events\DiscordMessageReceived;
use App\Listeners\HandleDiscordListener;
use CharlotteDunois\Yasmin\Models\Guild;
use CharlotteDunois\Yasmin\Models\Message;
use CharlotteDunois\Yasmin\Models\TextChannel;
use CharlotteDunois\Yasmin\Models\User;

/**
 * Tests for Discord message event listener.
 * @group current
 * @group discord
 * @group events
 */
final class HandleDiscordListenerTest extends \Tests\TestCase
{
    /**
     * Test handling a Discord event.
     * @test
     */
    public function testHandle(): void
    {
        $serverStub = $this->createStub(Guild::class);
        $serverStub->method('__get')->willReturn(\Str::random(10));

        $channelMap = [
            ['name', \Str::random(12)],
            ['guild', $serverStub],
        ];
        $channelMock = $this->createMock(TextChannel::class);
        $channelMock->method('__get')->will(self::returnValueMap($channelMap));
        $channelMock->expects(self::once())
            ->method('send')
            ->with(self::equalTo('Hello there!'));

        $messageMap = [
            ['author', $this->createStub(User::class)],
            ['channel', $channelMock],
            ['content', '/roll foo'],
        ];
        $messageStub = $this->createStub(Message::class);
        $messageStub->method('__get')->will(self::returnValueMap($messageMap));

        $event = new DiscordMessageReceived($messageStub);
        self::assertTrue((new HandleDiscordListener())->handle($event));
    }
}
