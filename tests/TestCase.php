<?php

declare(strict_types=1);

namespace Tests;

use Discord\Parts\Channel\Channel;
use Discord\Parts\Channel\Message;
use Discord\Parts\Guild\Guild;
use Discord\Parts\User\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Str;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;

    /**
     * Create a mock Discord message.
     * @param string $content Content of the message to Discord
     * @return Message
     */
    protected function createDiscordMessageMock(string $content): Message
    {
        $serverNameAndId = Str::random(10);
        $serverStub = self::createStub(Guild::class);
        $serverStub->method('__get')->willReturn($serverNameAndId);

        $userTag = 'user#' . random_int(1000, 9999);
        $userId = random_int(1, 9999);
        $userMap = [
            ['displayname', $userTag],
            ['id', $userId],
        ];
        $userMock = $this->createMock(User::class);
        $userMock->method('__get')->willReturnMap($userMap);

        $channelName = Str::random(12);
        $channelId = Str::random(10);
        $channelMap = [
            ['guild', $serverStub],
            ['id', $channelId],
            ['name', $channelName],
        ];
        $channelMock = $this->createMock(Channel::class);
        $channelMock->method('__get')->willReturnMap($channelMap);

        $messageMap = [
            ['author', $userMock],
            ['channel', $channelMock],
            ['content', $content],
        ];
        $messageMock = self::createStub(Message::class);
        $messageMock->method('__get')->willReturnMap($messageMap);
        return $messageMock;
    }
}
