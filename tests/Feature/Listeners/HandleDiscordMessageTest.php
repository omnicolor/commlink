<?php

declare(strict_types=1);

namespace Tests\Feature\Listeners;

use App\Events\DiscordMessageReceived;
use App\Events\DiscordUserLinked;
use App\Events\RollEvent;
use App\Listeners\HandleDiscordMessage;
use App\Models\Campaign;
use App\Models\Channel;
use App\Models\ChatUser;
use Discord\Discord;
use Discord\Parts\Channel\Channel as TextChannel;
use Discord\Parts\Channel\Message;
use Discord\Parts\Guild\Guild;
use Discord\Parts\User\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

/**
 * Tests for Discord message event listener.
 * @group discord
 * @group events
 * @medium
 */
final class HandleDiscordMessageTest extends TestCase
{
    use PHPMock;
    use WithFaker;

    /**
     * Mock random_int function to take randomness out of testing.
     * @var MockObject
     */
    protected MockObject $randomInt;

    /**
     * Set up the mock random function each time.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->randomInt = $this->getFunctionMock('App\\Rolls', 'random_int');
    }

    /**
     * Return a Discord username.
     * @return string
     */
    protected function createDiscordTag(): string
    {
        return $this->faker->word . '#' . $this->faker->randomNumber(4, true);
    }

    /**
     * Test handling a Discord event with an invalid command.
     * @test
     */
    public function testHandleInvalidCommand(): void
    {
        $serverStub = self::createStub(Guild::class);
        $serverStub->method('__get')->willReturn(\Str::random(10));

        $channelMap = [
            ['id', \Str::random(14)],
            ['name', \Str::random(12)],
            ['guild', $serverStub],
        ];
        $channelMock = $this->createMock(TextChannel::class);
        $channelMock->method('__get')->willReturnMap($channelMap);
        $channelMock->expects(self::once())
            ->method('sendMessage')
            ->with(self::equalTo('That doesn\'t appear to be a valid command!'));
        Channel::factory()->create([
            'channel_id' => $channelMock->id,
            'server_id' => $serverStub->id,
            'system' => 'dnd5e',
            'type' => 'discord',
        ]);

        $tag = $this->createDiscordTag();
        $userMap = [
            ['id', $this->faker->randomNumber(5)],
            ['displayname', $tag],
        ];
        $userMock = $this->createMock(User::class);
        $userMock->method('__get')->willReturnMap($userMap);

        $messageMap = [
            ['author', $userMock],
            ['channel', $channelMock],
            ['content', '/roll 6'],
        ];
        $messageStub = self::createStub(Message::class);
        $messageStub->method('__get')->willReturnMap($messageMap);

        $event = new DiscordMessageReceived(
            $messageStub,
            self::createStub(Discord::class)
        );
        self::assertTrue((new HandleDiscordMessage())->handle($event));
    }

    /**
     * Test handling a generic roll.
     * @test
     */
    public function testHandleGenericRoll(): void
    {
        $this->randomInt->expects(self::exactly(2))->willReturn(3);
        $tag = $this->createDiscordTag();
        $expected = sprintf('**%s rolled 6**', $tag) . \PHP_EOL
            . 'Rolling: 2d6 = [6] = 6' . \PHP_EOL
            . '_Rolls: 3, 3_';

        $serverStub = self::createStub(Guild::class);
        $serverStub->method('__get')->willReturn(\Str::random(10));

        $channelMap = [
            ['name', \Str::random(12)],
            ['guild', $serverStub],
        ];
        $channelMock = $this->createMock(TextChannel::class);
        $channelMock->method('__get')->willReturnMap($channelMap);
        $channelMock->expects(self::once())
            ->method('sendMessage')
            ->with(self::equalTo($expected));

        $userMap = [
            ['id', $this->faker->randomNumber(5)],
            ['displayname', $tag],
        ];
        $userMock = $this->createMock(User::class);
        $userMock->method('__get')->willReturnMap($userMap);

        $messageMap = [
            ['author', $userMock],
            ['channel', $channelMock],
            ['content', '/roll 2d6'],
        ];
        $messageStub = self::createStub(Message::class);
        $messageStub->method('__get')->willReturnMap($messageMap);

        $event = new DiscordMessageReceived(
            $messageStub,
            self::createStub(Discord::class)
        );
        self::assertTrue((new HandleDiscordMessage())->handle($event));
    }

    /**
     * Test handling a Discord event meriting a Discord response.
     * @test
     */
    public function testHandleInfoResponse(): void
    {
        $serverNameAndId = \Str::random(10);
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
            . 'Character: No character' . \PHP_EOL
            . 'Campaign: No campaign';
        $channelMap = [
            ['guild', $serverStub],
            ['id', $channelId],
            ['name', $channelName],
        ];
        $channelMock = $this->createMock(TextChannel::class);
        $channelMock->method('__get')->willReturnMap($channelMap);
        $channelMock->expects(self::once())
            ->method('sendMessage')
            ->with(self::equalTo($expected));

        $messageMap = [
            ['author', $userMock],
            ['channel', $channelMock],
            ['content', '/roll info'],
        ];
        $messageStub = self::createStub(Message::class);
        $messageStub->method('__get')->willReturnMap($messageMap);

        $event = new DiscordMessageReceived(
            $messageStub,
            self::createStub(Discord::class)
        );
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
            ['content', '/roll validate'],
        ];
        $messageMock = self::createStub(Message::class);
        $messageMock->method('__get')->willReturnMap($messageMap);
        // @phpstan-ignore-next-line
        $messageMock->expects(self::once())
            ->method('reply')
            ->with($expected);

        $event = new DiscordMessageReceived(
            $messageMock,
            self::createStub(Discord::class)
        );
        self::assertTrue((new HandleDiscordMessage())->handle($event));
        Event::assertNotDispatched(DiscordUserLinked::class);
    }

    /**
     * Test handling a Discord event for linking a user with a wrong hash.
     * @test
     */
    public function testHandleValidateUserWrongHash(): void
    {
        Event::fake();

        $serverNameAndId = \Str::random(10);
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
            ['content', '/roll validate deadb33f'],
        ];
        $messageMock = self::createStub(Message::class);
        $messageMock->method('__get')->willReturnMap($messageMap);
        // @phpstan-ignore-next-line
        $messageMock->expects(self::once())
            ->method('reply')
            ->with($expected);

        $event = new DiscordMessageReceived(
            $messageMock,
            self::createStub(Discord::class)
        );
        self::assertTrue((new HandleDiscordMessage())->handle($event));
        Event::assertNotDispatched(DiscordUserLinked::class);
    }

    /**
     * Test handling a Discord event for linking a user already linked.
     * @test
     */
    public function testHandleValidateUserAlreadyLinked(): void
    {
        Event::fake();

        $serverNameAndId = \Str::random(10);
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
            ['content', sprintf('/roll validate %s', $chatUser->verification)],
        ];
        $messageMock = self::createStub(Message::class);
        $messageMock->method('__get')->willReturnMap($messageMap);
        // @phpstan-ignore-next-line
        $messageMock->expects(self::once())
            ->method('reply')
            ->with($expected);

        $event = new DiscordMessageReceived(
            $messageMock,
            self::createStub(Discord::class)
        );
        self::assertTrue((new HandleDiscordMessage())->handle($event));
        Event::assertNotDispatched(DiscordUserLinked::class);
    }

    /**
     * Test handling a Discord event for linking a user.
     * @test
     */
    public function testHandleValidateUser(): void
    {
        Event::fake();

        $serverNameAndId = \Str::random(10);
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
            ['content', sprintf('/roll validate %s', $chatUser->verification)],
        ];
        $messageMock = self::createStub(Message::class);
        $messageMock->method('__get')->willReturnMap($messageMap);
        // @phpstan-ignore-next-line
        $messageMock->expects(self::once())
            ->method('reply')
            ->with($expected);

        $event = new DiscordMessageReceived(
            $messageMock,
            self::createStub(Discord::class)
        );
        self::assertTrue((new HandleDiscordMessage())->handle($event));
        Event::assertDispatched(DiscordUserLinked::class);
    }

    /**
     * Test handling a Discord message asking for a system-specific number roll.
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

        $serverStub = self::createStub(Guild::class);
        $serverStub->method('__get')->willReturn(\Str::random(10));

        $channelMap = [
            ['id', \Str::random(14)],
            ['name', \Str::random(12)],
            ['guild', $serverStub],
        ];
        $channelMock = $this->createMock(TextChannel::class);
        $channelMock->method('__get')->willReturnMap($channelMap);
        Channel::factory()->create([
            'channel_id' => $channelMock->id,
            'server_id' => $serverStub->id,
            'system' => 'cyberpunkred',
            'type' => Channel::TYPE_DISCORD,
        ]);

        $userMock = $this->createMock(User::class);
        $userMock->method('__get')->willReturn('discord#tag');

        $messageMap = [
            ['author', $userMock],
            ['channel', $channelMock],
            ['content', '/roll 6'],
        ];
        $messageMock = $this->createMock(Message::class);
        $messageMock->method('__get')->willReturnMap($messageMap);
        $messageMock->expects(self::once())
            ->method('reply')
            ->with(self::equalTo($expected));

        $event = new DiscordMessageReceived(
            $messageMock,
            self::createStub(Discord::class)
        );
        self::assertTrue((new HandleDiscordMessage())->handle($event));
    }

    /**
     * Test handling a Discord message asking for a system-specific, non-help
     * roll.
     * @test
     */
    public function testHandleSystemNonNumberRoll(): void
    {
        Event::fake();

        $serverStub = self::createStub(Guild::class);
        $serverStub->method('__get')->willReturn(\Str::random(10));

        $channelMap = [
            ['id', \Str::random(14)],
            ['name', \Str::random(12)],
            ['guild', $serverStub],
        ];
        $channelMock = $this->createMock(TextChannel::class);
        $channelMock->method('__get')->willReturnMap($channelMap);
        $campaign = Campaign::factory()->create([
            'options' => [
                'nightCityTarot' => true,
            ],
            'system' => 'cyberpunkred',
        ]);
        Channel::factory()->create([
            'campaign_id' => $campaign,
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
            ['content', '/roll tarot'],
        ];
        $messageStub = self::createStub(Message::class);
        $messageStub->method('__get')->willReturnMap($messageMap);

        $event = new DiscordMessageReceived(
            $messageStub,
            self::createStub(Discord::class)
        );
        self::assertTrue((new HandleDiscordMessage())->handle($event));
        Event::assertDispatched(RollEvent::class);
    }

    /**
     * Test handling a Discord message asking for a system-specific help roll.
     * @test
     */
    public function testHandleSystemHelpRoll(): void
    {
        $expected = 'Slack/Discord bot that lets you roll Shadowrun 5E dice.';

        $serverStub = self::createStub(Guild::class);
        $serverStub->method('__get')->willReturn(\Str::random(10));

        $channelMap = [
            ['id', \Str::random(14)],
            ['name', \Str::random(12)],
            ['guild', $serverStub],
        ];
        $channelMock = $this->createMock(TextChannel::class);
        $channelMock->method('__get')->willReturnMap($channelMap);
        $channelMock->expects(self::once())
            ->method('sendMessage')
            ->with(self::stringContains($expected));
        Channel::factory()->create([
            'channel_id' => $channelMock->id,
            'server_id' => $serverStub->id,
            'system' => 'shadowrun5e',
            'type' => 'discord',
        ]);

        $userMock = $this->createMock(User::class);
        $userMock->method('__get')->willReturn('discord#tag');

        $messageMap = [
            ['author', $userMock],
            ['channel', $channelMock],
            ['content', '/roll help'],
        ];
        $messageStub = self::createStub(Message::class);
        $messageStub->method('__get')->willReturnMap($messageMap);

        $event = new DiscordMessageReceived(
            $messageStub,
            self::createStub(Discord::class)
        );
        self::assertTrue((new HandleDiscordMessage())->handle($event));
    }

    /**
     * Test handling a Discord message asking for a generic help roll.
     * @test
     */
    public function testHandleNonSystemHelpRoll(): void
    {
        $expected = sprintf('**About %s**', config('app.name'));

        $serverStub = self::createStub(Guild::class);
        $serverStub->method('__get')->willReturn(\Str::random(10));

        $channelMap = [
            ['id', \Str::random(14)],
            ['name', \Str::random(12)],
            ['guild', $serverStub],
        ];
        $channelMock = $this->createMock(TextChannel::class);
        $channelMock->method('__get')->willReturnMap($channelMap);
        $channelMock->expects(self::once())
            ->method('sendMessage')
            ->with(self::stringContains($expected));

        $userMock = $this->createMock(User::class);
        $userMock->method('__get')->willReturn('discord#tag');

        $messageMap = [
            ['author', $userMock],
            ['channel', $channelMock],
            ['content', '/roll help'],
        ];
        $messageStub = self::createStub(Message::class);
        $messageStub->method('__get')->willReturnMap($messageMap);

        $event = new DiscordMessageReceived(
            $messageStub,
            self::createStub(Discord::class)
        );
        self::assertTrue((new HandleDiscordMessage())->handle($event));
    }
}
