<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Responses\Discord;

use App\Events\ChannelLinked;
use App\Events\DiscordMessageReceived;
use App\Http\Responses\Discord\RegisterResponse;
use App\Models\Channel;
use App\Models\ChatUser;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;

/**
 * @group discord
 * @medium
 */
final class RegisterResponseTest extends \Tests\TestCase
{
    /**
     * Test trying to handle a response missing the system code.
     * @test
     */
    public function testRegisterWithoutSystem(): void
    {
        Event::fake();
        Http::fake();

        $expected = 'To register a channel, use `register [system]`, where '
            . 'system is a system code: '
            . implode(', ', array_keys(config('app.systems')));
        $messageMock = $this->createDiscordMessageMock('/roll register');
        // @phpstan-ignore-next-line
        $messageMock->expects(self::once())
            ->method('reply')
            ->with($expected);
        $event = new DiscordMessageReceived($messageMock);
        self::assertSame('', (string)(new RegisterResponse($event)));

        Event::assertNotDispatched(ChannelLinked::class);
        Http::assertNothingSent();
    }

    /**
     * Test trying to handle a user trying to register for an invalid system.
     * @test
     */
    public function testRegisterWithInvalidSystem(): void
    {
        Event::fake();
        Http::fake();

        $expected = sprintf(
            '"invalid" is not a valid system code. Use `register '
                . '<system>`, where system is one of: %s',
            \implode(', ', \array_keys(config('app.systems'))),
        );
        $messageMock = $this->createDiscordMessageMock('/roll register invalid');
        // @phpstan-ignore-next-line
        $messageMock->expects(self::once())
            ->method('reply')
            ->with($expected);
        $event = new DiscordMessageReceived($messageMock);

        self::assertSame('', (string)(new RegisterResponse($event)));

        Event::assertNotDispatched(ChannelLinked::class);
        Http::assertNothingSent();
    }

    /**
     * Test trying to register a channel that's already registered.
     * @test
     */
    public function testRegisterAlreadyRegistered(): void
    {
        Event::fake();
        Http::fake();

        $expected = 'This channel is already registered for "dnd5e"';
        $messageMock = $this->createDiscordMessageMock('/roll register shadowrun5e');
        // @phpstan-ignore-next-line
        $messageMock->expects(self::once())
            ->method('reply')
            ->with($expected);

        $event = new DiscordMessageReceived($messageMock);
        Channel::factory()->create([
            'channel_id' => $event->channel->id,
            'server_id' => $event->server->id,
            'system' => 'dnd5e',
            'type' => Channel::TYPE_DISCORD,
        ]);

        self::assertSame('', (string)(new RegisterResponse($event)));

        Event::assertNotDispatched(ChannelLinked::class);
        Http::assertNothingSent();
    }

    /**
     * Test trying to register an unregistered channel from an unregistered
     * user.
     * @test
     */
    public function testRegisterWithoutChatUser(): void
    {
        Event::fake();
        Http::fake();

        $expected = \sprintf(
            'You must have already created an account on %s (%s) and '
                . 'linked it to this server before you can register a '
                . 'channel to a specific system.',
            config('app.name'),
            config('app.url') . '/settings',
        );
        $messageMock = $this->createDiscordMessageMock('/roll register shadowrun5e');
        // @phpstan-ignore-next-line
        $messageMock->expects(self::once())
            ->method('reply')
            ->with($expected);

        $event = new DiscordMessageReceived($messageMock);
        self::assertSame('', (string)(new RegisterResponse($event)));

        Event::assertNotDispatched(ChannelLinked::class);
        Http::assertNothingSent();
    }

    /**
     * Test successfully registering a channel, but Discord's API fails.
     * @test
     */
    public function testRegisterDiscordFails(): void
    {
        Event::fake();

        $messageMock = $this->createDiscordMessageMock('/roll register shadowrun5e');

        $event = new DiscordMessageReceived($messageMock);
        // @phpstan-ignore-next-line
        $event->channel->expects(self::once())->method('sendMessage');
        ChatUser::factory()->create([
            'remote_user_id' => optional($event->user)->id,
            'server_id' => $event->server->id,
            'server_type' => ChatUser::TYPE_DISCORD,
            'verified' => true,
        ]);
        $expected = \sprintf(
            '%s has registered this channel for the "Shadowrun 5th Edition" system.',
            // @phpstan-ignore-next-line
            $event->channel->username,
        );

        $guildResponse = Http::response([], Response::HTTP_NOT_FOUND, []);
        $hookResponse = Http::response([], Response::HTTP_NOT_FOUND, []);
        $channelResponse = Http::response([], Response::HTTP_NOT_FOUND, []);
        Http::fake([
            sprintf('https://discord.com/api/guilds/%s', $event->server->id) => $guildResponse,
            sprintf('https://discord.com/api/channels/%s/webhooks', $event->channel->id) => $hookResponse,
            sprintf('https://discord.com/api/channels/%s', $event->channel->id) => $channelResponse,
        ]);

        self::assertSame('', (string)(new RegisterResponse($event)));

        Event::assertDispatched(function (ChannelLinked $event): bool {
            return null === $event->channel->server_name
                && null === $event->channel->channel_name;
        });
        Http::assertSent(function (Request $request) use ($event): bool {
            $urls = [
                sprintf('https://discord.com/api/channels/%d/webhooks', $event->channel->id),
                sprintf('https://discord.com/api/guilds/%s', $event->server->id),
                sprintf('https://discord.com/api/channels/%s', $event->channel->id),
            ];
            return in_array($request->url(), $urls, true);
        });
    }

    /**
     * Test successfully registering a channel.
     * @test
     */
    public function testRegister(): void
    {
        Event::fake();

        $messageMock = $this->createDiscordMessageMock('/roll register shadowrun5e');

        $event = new DiscordMessageReceived($messageMock);
        // @phpstan-ignore-next-line
        $event->channel->expects(self::once())->method('sendMessage');
        ChatUser::factory()->create([
            'remote_user_id' => optional($event->user)->id,
            'server_id' => $event->server->id,
            'server_type' => ChatUser::TYPE_DISCORD,
            'verified' => true,
        ]);
        $expected = \sprintf(
            '%s has registered this channel for the "Shadowrun 5th Edition" system.',
            // @phpstan-ignore-next-line
            $event->channel->username,
        );

        $guildResponse = Http::response(
            ['name' => 'Guild Name'],
            Response::HTTP_OK,
            []
        );
        $hookResponse = Http::response(
            ['id' => 12345, 'token' => 'abc123'],
            Response::HTTP_OK,
            []
        );
        $channelResponse = Http::response(
            ['name' => 'Channel Name'],
            Response::HTTP_OK,
            []
        );
        Http::fake([
            sprintf('https://discord.com/api/guilds/%s', $event->server->id) => $guildResponse,
            sprintf('https://discord.com/api/channels/%s/webhooks', $event->channel->id) => $hookResponse,
            sprintf('https://discord.com/api/channels/%s', $event->channel->id) => $channelResponse,
        ]);

        self::assertSame('', (string)(new RegisterResponse($event)));

        Event::assertDispatched(function (ChannelLinked $event): bool {
            return 'Guild Name' === $event->channel->server_name
                && 'Channel Name' === $event->channel->channel_name;
        });
        Http::assertSent(function (Request $request) use ($event): bool {
            $urls = [
                sprintf('https://discord.com/api/channels/%d/webhooks', $event->channel->id),
                sprintf('https://discord.com/api/guilds/%s', $event->server->id),
                sprintf('https://discord.com/api/channels/%s', $event->channel->id),
            ];
            return in_array($request->url(), $urls, true);
        });
    }
}
