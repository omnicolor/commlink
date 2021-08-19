<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Responses\Discord;

use App\Events\DiscordMessageReceived;
use App\Http\Responses\Discord\HelpResponse;
use App\Models\Channel;
use App\Models\ChatUser;
use CharlotteDunois\Yasmin\Models\Guild;
use CharlotteDunois\Yasmin\Models\Message;
use CharlotteDunois\Yasmin\Models\TextChannel;
use CharlotteDunois\Yasmin\Models\User;

/**
 * @group discord
 * @medium
 */
final class HelpResponseTest extends \Tests\TestCase
{
    /**
     * Create a mock Discord message.
     * @return Message
     */
    protected function createMessageMock(): Message
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
            ['content', '/roll help'],
        ];
        $messageMock = $this->createStub(Message::class);
        $messageMock->method('__get')->willReturnMap($messageMap);
        return $messageMock;
    }

    /**
     * Test trying to get help in an unregistered channel as an unregistered
     * user.
     * @small
     * @test
     */
    public function testHelpUnregistered(): void
    {
        $expected = \sprintf(
            '%1$s is a Discord bot that lets you roll dice appropriate for '
                . 'various RPG systems. For example, if you are playing The '
                . 'Expanse, it will roll three dice, marking one of them as '
                . 'the "drama die", adding up the result with the number you '
                . 'give for your attribute+focus score, and return the result '
                . 'along with any stunt points.' . \PHP_EOL . \PHP_EOL
                . 'If your game uses the web app for %1$s (%2$s) as well, '
                . 'links in the app will automatically roll in Discord, and '
                . 'changes made to your character via Discord will appear in '
                . '%1$s.' . \PHP_EOL . \PHP_EOL,
            config('app.name'),
            config('app.url')
        )
            . '**Commands for unregistered channels:**' . \PHP_EOL
            . '· `help` - Show help' . \PHP_EOL
            . '· `XdY[+C] [text]` - Roll X dice with Y sides, '
            . 'optionally adding C to the result, optionally '
            . 'describing that the roll is for "text"';

        $messageMock = $this->createMessageMock();
        $event = new DiscordMessageReceived($messageMock);
        $response = new HelpResponse($event);
        self::assertSame($expected, (string)$response);
    }

    /**
     * Test trying to get help in a registered channel with an unregistered
     * user.
     * @test
     */
    public function testHelpRegisteredChannelUnregisteredUser(): void
    {
        $messageMock = $this->createMessageMock();
        $expected = \sprintf(
            '%1$s is a Discord bot that lets you roll dice appropriate for '
                . 'various RPG systems. For example, if you are playing The '
                . 'Expanse, it will roll three dice, marking one of them as '
                . 'the "drama die", adding up the result with the number you '
                . 'give for your attribute+focus score, and return the result '
                . 'along with any stunt points.' . \PHP_EOL . \PHP_EOL
                . 'If your game uses the web app for %1$s (%2$s) as well, '
                . 'links in the app will automatically roll in Discord, and '
                . 'changes made to your character via Discord will appear in '
                . '%1$s.' . \PHP_EOL . \PHP_EOL,
            config('app.name'),
            config('app.url')
        )
            . 'This channel is registered for Shadowrun 5th Edition.'
            . \PHP_EOL . \PHP_EOL
            . \sprintf(
                'Your Slack user has not been linked with a %s user. Go to the '
                    . 'settings page (%s/settings) and copy the command listed '
                    . 'there for this server. If the server isn\'t listed, '
                    . 'follow the instructions there to add it. You\'ll need '
                    . 'to know your server ID (`%s`) and your user ID (`%s`).',
                config('app.name'),
                config('app.url'),
                // @phpstan-ignore-next-line
                $messageMock->channel->guild->id,
                $messageMock->author->id,
            );

        $channel = Channel::factory()->create([
            // @phpstan-ignore-next-line
            'channel_id' => $messageMock->channel->id,
            // @phpstan-ignore-next-line
            'server_id' => $messageMock->channel->guild->id,
            'system' => 'shadowrun5e',
            'type' => 'discord',
        ]);
        $event = new DiscordMessageReceived($messageMock);
        $response = new HelpResponse($event);
        self::assertSame($expected, (string)$response);
    }

    /**
     * Test trying to get help with both channel and user registered.
     * @test
     */
    public function testHelpRegisteredUserAndChannel(): void
    {
        $messageMock = $this->createMessageMock();
        $expected = \sprintf(
            '%1$s is a Discord bot that lets you roll dice appropriate for '
                . 'various RPG systems. For example, if you are playing The '
                . 'Expanse, it will roll three dice, marking one of them as '
                . 'the "drama die", adding up the result with the number you '
                . 'give for your attribute+focus score, and return the result '
                . 'along with any stunt points.' . \PHP_EOL . \PHP_EOL
                . 'If your game uses the web app for %1$s (%2$s) as well, '
                . 'links in the app will automatically roll in Discord, and '
                . 'changes made to your character via Discord will appear in '
                . '%1$s.' . \PHP_EOL . \PHP_EOL,
            config('app.name'),
            config('app.url')
        )
            . 'This channel is registered for Shadowrun 5th Edition.'
            . \PHP_EOL . \PHP_EOL;

        /** @var ChatUser */
        $chatUser = ChatUser::factory()->create([
            // @phpstan-ignore-next-line
            'remote_user_id' => (string)$messageMock->author->id,
            // @phpstan-ignore-next-line
            'server_id' => $messageMock->channel->guild->id,
            'server_type' => 'discord',
            'verified' => true,
        ]);
        /** @var Channel */
        $channel = Channel::factory()->create([
            // @phpstan-ignore-next-line
            'channel_id' => $messageMock->channel->id,
            // @phpstan-ignore-next-line
            'server_id' => $messageMock->channel->guild->id,
            'system' => 'shadowrun5e',
            'type' => 'discord',
        ]);
        // @phpstan-ignore-next-line
        $channel->user = (string)$messageMock->author->id;
        $event = new DiscordMessageReceived($messageMock);
        $response = new HelpResponse($event);
        self::assertSame($expected, (string)$response);
    }
}
