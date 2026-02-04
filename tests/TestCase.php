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
use PHPUnit\Framework\MockObject\MockObject;

use function random_int;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;

    protected function createDiscordMessageMock(string $content): Message&MockObject
    {
        $server_name_and_id = Str::random(10);
        $server_stub = self::createStub(Guild::class);
        $server_stub->method('__get')->willReturn($server_name_and_id);

        $user_tag = 'user#' . random_int(1000, 9999);
        $user_id = random_int(1, 9999);
        $user_map = [
            ['displayname', $user_tag],
            ['id', $user_id],
        ];

        $user_stub = self::createStub(User::class);
        $user_stub->method('__get')->willReturnMap($user_map);

        $channel_name = Str::random(12);
        $channel_id = Str::random(10);
        $channel_map = [
            ['guild', $server_stub],
            ['id', $channel_id],
            ['name', $channel_name],
        ];

        $channel_stub = self::createStub(Channel::class);
        $channel_stub->method('__get')->willReturnMap($channel_map);

        $message_map = [
            ['author', $user_stub],
            ['channel', $channel_stub],
            ['content', $content],
        ];
        $messageMock = self::createMock(Message::class);
        $messageMock->method('__get')->willReturnMap($message_map);
        return $messageMock;
    }
}
