<?php

declare(strict_types=1);

namespace Tests\Feature\Listeners;

use App\Events\DiscordMessageReceived;
use App\Events\DiscordUserLinked;
use App\Listeners\HandleDiscordMessage;
use App\Models\Channel;
use App\Models\ChatUser;
use CharlotteDunois\Yasmin\Models\Guild;
use CharlotteDunois\Yasmin\Models\Message;
use CharlotteDunois\Yasmin\Models\TextChannel;
use CharlotteDunois\Yasmin\Models\User;
use Illuminate\Support\Facades\Event;

/**
 * Tests for Discord message event listener.
 * @group discord
 * @group events
 * @small
 */
final class HandleDiscordMessageTest extends \Tests\TestCase
{
    use \phpmock\phpunit\PHPMock;

    /**
     * Mock random_int function to take randomness out of testing.
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected \PHPUnit\Framework\MockObject\MockObject $randomInt;

    /**
     * Set up the mock random function each time.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->randomInt = $this->getFunctionMock('App\\Rolls', 'random_int');
    }

    /**
     * Test handling a Discord event with an invalid command.
     * @medium
     * @test
     */
    public function testHandleInvalidCommand(): void
    {
        $serverStub = $this->createStub(Guild::class);
        $serverStub->method('__get')->willReturn(\Str::random(10));

        $channelMap = [
            ['id', \Str::random(14)],
            ['name', \Str::random(12)],
            ['guild', $serverStub],
        ];
        $channelMock = $this->createMock(TextChannel::class);
        $channelMock->method('__get')->willReturnMap($channelMap);
        $channelMock->expects(self::once())
            ->method('send')
            ->with(self::equalTo('That doesn\'t appear to be a valid command!'));
        Channel::factory()->create([
            'channel_id' => $channelMock->id,
            'server_id' => $serverStub->id,
            'system' => 'dnd5e',
            'type' => 'discord',
        ]);

        $messageMap = [
            ['author', $this->createStub(User::class)],
            ['channel', $channelMock],
            ['content', '/roll 6'],
        ];
        $messageStub = $this->createStub(Message::class);
        $messageStub->method('__get')->willReturnMap($messageMap);

        $event = new DiscordMessageReceived($messageStub);
        self::assertTrue((new HandleDiscordMessage())->handle($event));
    }

    /**
     * Test handling a generic roll.
     * @test
     */
    public function testHandleGenericRoll(): void
    {
        $this->randomInt->expects(self::exactly(2))->willReturn(3);
        $expected = '**discord#tag rolled 6**' . \PHP_EOL
            . 'Rolling: 2d6 = [6] = 6' . \PHP_EOL
            . '_Rolls: 3, 3_';

        $serverStub = $this->createStub(Guild::class);
        $serverStub->method('__get')->willReturn(\Str::random(10));

        $channelMap = [
            ['name', \Str::random(12)],
            ['guild', $serverStub],
        ];
        $channelMock = $this->createMock(TextChannel::class);
        $channelMock->method('__get')->willReturnMap($channelMap);
        $channelMock->expects(self::once())
            ->method('send')
            ->with(self::equalTo($expected));

        $userMock = $this->createMock(User::class);
        $userMock->method('__get')->willReturn('discord#tag');

        $messageMap = [
            ['author', $userMock],
            ['channel', $channelMock],
            ['content', '/roll 2d6'],
        ];
        $messageStub = $this->createStub(Message::class);
        $messageStub->method('__get')->willReturnMap($messageMap);

        $event = new DiscordMessageReceived($messageStub);
        self::assertTrue((new HandleDiscordMessage())->handle($event));
    }

    /**
     * Test handling a Discord event meriting a Discord response.
     * @test
     */
    public function testHandleInfoResponse(): void
    {
        $serverNameAndId = \Str::random(10);
        $serverStub = $this->createStub(Guild::class);
        $serverStub->method('__get')->willReturn($serverNameAndId);

        $userTag = 'user#' . random_int(1000, 9999);
        $userId = random_int(1, 9999);
        $userMap = [
            ['tag', $userTag],
            ['id', $userId],
        ];
        $userMock = $this->createMock(User::class);
        $userMock->method('__get')->willReturnMap($userMap);

        $channelName = \Str::random(12);
        $channelId = \Str::random(10);
        $expected = '**Debugging info**' . \PHP_EOL
            . 'User Tag: ' . $userTag . \PHP_EOL
            . 'User ID: ' . $userId . \PHP_EOL
            . 'Server Name: ' . $serverNameAndId . \PHP_EOL
            . 'Server ID: ' . $serverNameAndId . \PHP_EOL
            . 'Channel Name: ' . $channelName . \PHP_EOL
            . 'Channel ID: ' . $channelId . \PHP_EOL
            . 'System: Unregistered' . \PHP_EOL
            . 'Character: Unlinked' . \PHP_EOL
            . 'Campaign: No campaign';
        $channelMap = [
            ['guild', $serverStub],
            ['id', $channelId],
            ['name', $channelName],
        ];
        $channelMock = $this->createMock(TextChannel::class);
        $channelMock->method('__get')->willReturnMap($channelMap);
        $channelMock->expects(self::once())
            ->method('send')
            ->with(self::equalTo($expected));

        $messageMap = [
            ['author', $userMock],
            ['channel', $channelMock],
            ['content', '/roll info'],
        ];
        $messageStub = $this->createStub(Message::class);
        $messageStub->method('__get')->willReturnMap($messageMap);

        $event = new DiscordMessageReceived($messageStub);
        self::assertTrue((new HandleDiscordMessage())->handle($event));
    }

    /**
     * Test handling a Discord event for linking a user without the hash.
     * @test
     */
    public function testHandleValidateUserNoHash(): void
    {
        Event::fake();

        $serverNameAndId = \Str::random(10);
        $serverStub = $this->createStub(Guild::class);
        $serverStub->method('__get')->willReturn($serverNameAndId);

        $userTag = 'user#' . random_int(1000, 9999);
        $userId = random_int(1, 9999);
        $userMap = [
            ['tag', $userTag],
            ['id', $userId],
        ];
        $userMock = $this->createMock(User::class);
        $userMock->method('__get')->willReturnMap($userMap);

        $channelName = \Str::random(12);
        $channelId = \Str::random(10);
        $expected = sprintf(
            'To link your Commlink user, go to the settings page (%s/settings) '
                . 'and copy the command listed there for this server. If the '
                . 'server isn\'t listed, follow the instructions there to add '
                . 'it. You\'ll need to know your server ID (`%s`) and your '
                . 'user ID (`%d`).',
            config('app.url'),
            $serverNameAndId,
            $userId,
        );
        $channelMap = [
            ['guild', $serverStub],
            ['id', $channelId],
            ['name', $channelName],
        ];
        $channelMock = $this->createMock(TextChannel::class);
        $channelMock->method('__get')->willReturnMap($channelMap);

        $messageMap = [
            ['author', $userMock],
            ['channel', $channelMock],
            ['content', '/roll validateUser'],
        ];
        $messageMock = $this->createStub(Message::class);
        $messageMock->method('__get')->willReturnMap($messageMap);
        // @phpstan-ignore-next-line
        $messageMock->expects(self::once())
            ->method('reply')
            ->with($expected);

        $event = new DiscordMessageReceived($messageMock);
        self::assertTrue((new HandleDiscordMessage())->handle($event));
        Event::assertNotDispatched(DiscordUserLinked::class);
    }

    /**
     * Test handling a Discord event for linking a user with a wrong hash.
     * @medium
     * @test
     */
    public function testHandleValidateUserWrongHash(): void
    {
        Event::fake();

        $serverNameAndId = \Str::random(10);
        $serverStub = $this->createStub(Guild::class);
        $serverStub->method('__get')->willReturn($serverNameAndId);

        $userTag = 'user#' . random_int(1000, 9999);
        $userId = random_int(1, 9999);
        $userMap = [
            ['tag', $userTag],
            ['id', $userId],
        ];
        $userMock = $this->createMock(User::class);
        $userMock->method('__get')->willReturnMap($userMap);

        $channelName = \Str::random(12);
        $channelId = \Str::random(10);
        $expected = sprintf(
            'We couldn\'t find a Commlink registration for this Discord server '
                . 'and your user. Go to the settings page (%s/settings) and '
                . 'copy the command listed there for this server. If the '
                . 'server isn\'t listed, follow the instructions there to add '
                . 'it. You\'ll need to know your server ID (`%s`) and your '
                . 'user ID (`%d`).',
            config('app.url'),
            $serverNameAndId,
            $userId,
        );
        $channelMap = [
            ['guild', $serverStub],
            ['id', $channelId],
            ['name', $channelName],
        ];
        $channelMock = $this->createMock(TextChannel::class);
        $channelMock->method('__get')->willReturnMap($channelMap);

        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $userId,
            'server_id' => $serverNameAndId,
            'server_type' => ChatUser::TYPE_DISCORD,
            'verified' => true,
        ]);

        $messageMap = [
            ['author', $userMock],
            ['channel', $channelMock],
            ['content', '/roll validateUser deadb33f'],
        ];
        $messageMock = $this->createStub(Message::class);
        $messageMock->method('__get')->willReturnMap($messageMap);
        // @phpstan-ignore-next-line
        $messageMock->expects(self::once())
            ->method('reply')
            ->with($expected);

        $event = new DiscordMessageReceived($messageMock);
        self::assertTrue((new HandleDiscordMessage())->handle($event));
        Event::assertNotDispatched(DiscordUserLinked::class);
    }

    /**
     * Test handling a Discord event for linking a user already linked.
     * @medium
     * @test
     */
    public function testHandleValidateUserAlreadyLinked(): void
    {
        Event::fake();

        $serverNameAndId = \Str::random(10);
        $serverStub = $this->createStub(Guild::class);
        $serverStub->method('__get')->willReturn($serverNameAndId);

        $userTag = 'user#' . random_int(1000, 9999);
        $userId = random_int(1, 9999);
        $userMap = [
            ['tag', $userTag],
            ['id', $userId],
        ];
        $userMock = $this->createMock(User::class);
        $userMock->method('__get')->willReturnMap($userMap);

        $channelName = \Str::random(12);
        $channelId = \Str::random(10);
        $expected = 'It looks like you\'re already verified!';
        $channelMap = [
            ['guild', $serverStub],
            ['id', $channelId],
            ['name', $channelName],
        ];
        $channelMock = $this->createMock(TextChannel::class);
        $channelMock->method('__get')->willReturnMap($channelMap);

        /** @var ChatUser */
        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $userId,
            'server_id' => $serverNameAndId,
            'server_type' => ChatUser::TYPE_DISCORD,
            'verified' => true,
        ]);

        $messageMap = [
            ['author', $userMock],
            ['channel', $channelMock],
            ['content', sprintf('/roll validateUser %s', $chatUser->verification)],
        ];
        $messageMock = $this->createStub(Message::class);
        $messageMock->method('__get')->willReturnMap($messageMap);
        // @phpstan-ignore-next-line
        $messageMock->expects(self::once())
            ->method('reply')
            ->with($expected);

        $event = new DiscordMessageReceived($messageMock);
        self::assertTrue((new HandleDiscordMessage())->handle($event));
        Event::assertNotDispatched(DiscordUserLinked::class);
    }

    /**
     * Test handling a Discord event for linking a user.
     * @medium
     * @test
     */
    public function testHandleValidateUser(): void
    {
        Event::fake();

        $serverNameAndId = \Str::random(10);
        $serverStub = $this->createStub(Guild::class);
        $serverStub->method('__get')->willReturn($serverNameAndId);

        $userTag = 'user#' . random_int(1000, 9999);
        $userId = random_int(1, 9999);
        $userMap = [
            ['tag', $userTag],
            ['id', $userId],
        ];
        $userMock = $this->createMock(User::class);
        $userMock->method('__get')->willReturnMap($userMap);

        $channelName = \Str::random(12);
        $channelId = \Str::random(10);
        $channelMap = [
            ['guild', $serverStub],
            ['id', $channelId],
            ['name', $channelName],
        ];
        $channelMock = $this->createMock(TextChannel::class);
        $channelMock->method('__get')->willReturnMap($channelMap);

        /** @var ChatUser */
        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $userId,
            'server_id' => $serverNameAndId,
            'server_type' => ChatUser::TYPE_DISCORD,
            'verified' => false,
        ]);

        $expected = 'Your Commlink account has been linked with this Discord '
            . 'user. You only need to do this once for this server, no matter '
            . 'how many different channels you play in.';
        $messageMap = [
            ['author', $userMock],
            ['channel', $channelMock],
            ['content', sprintf('/roll validateUser %s', $chatUser->verification)],
        ];
        $messageMock = $this->createStub(Message::class);
        $messageMock->method('__get')->willReturnMap($messageMap);
        // @phpstan-ignore-next-line
        $messageMock->expects(self::once())
            ->method('reply')
            ->with($expected);

        $event = new DiscordMessageReceived($messageMock);
        self::assertTrue((new HandleDiscordMessage())->handle($event));
        Event::assertDispatched(DiscordUserLinked::class);
    }

    /**
     * Test handling a Discord message asking for a system-specific number roll.
     * @medium
     * @test
     */
    public function testHandleSystemNumberRoll(): void
    {
        $this->randomInt = $this->getFunctionMock(
            'App\\Rolls\Cyberpunkred',
            'random_int'
        );
        $this->randomInt->expects(self::exactly(1))->willReturn(3);
        $expected = "**discord#tag made a roll**\n1d10 + 6 = 3 + 6 = 9";

        $serverStub = $this->createStub(Guild::class);
        $serverStub->method('__get')->willReturn(\Str::random(10));

        $channelMap = [
            ['id', \Str::random(14)],
            ['name', \Str::random(12)],
            ['guild', $serverStub],
        ];
        $channelMock = $this->createMock(TextChannel::class);
        $channelMock->method('__get')->willReturnMap($channelMap);
        $channelMock->expects(self::once())
            ->method('send')
            ->with(self::equalTo($expected));
        Channel::factory()->create([
            'channel_id' => $channelMock->id,
            'server_id' => $serverStub->id,
            'system' => 'cyberpunkred',
            'type' => 'discord',
        ]);

        $userMock = $this->createMock(User::class);
        $userMock->method('__get')->willReturn('discord#tag');

        $messageMap = [
            ['author', $userMock],
            ['channel', $channelMock],
            ['content', '/roll 6'],
        ];
        $messageStub = $this->createStub(Message::class);
        $messageStub->method('__get')->willReturnMap($messageMap);

        $event = new DiscordMessageReceived($messageStub);
        self::assertTrue((new HandleDiscordMessage())->handle($event));
    }
}
