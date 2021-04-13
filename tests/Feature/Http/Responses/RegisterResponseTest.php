<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Responses;

use App\Exceptions\SlackException;
use App\Http\Responses\RegisterResponse;
use App\Models\Channel;
use App\Models\ChatUser;
use App\Models\User;

/**
 * Tests for registering a channel in Slack.
 * @covers \App\Http\Responses\RegisterResponse
 * @group slack
 */
final class RegisterResponseTest extends \Tests\TestCase
{
    /**
     * Test trying to register a channel without passing in the channel.
     * @test
     */
    public function testChannelMissing(): void
    {
        self::expectException(SlackException::class);
        self::expectExceptionMessage('Channel is required');
        $response = new RegisterResponse();
    }

    /**
     * Test trying to register a system without creating a Commlink account.
     * @test
     */
    public function testRegisterNoAccount(): void
    {
        $user = User::factory()->create();
        $channel = new Channel([
            'channel_id' => 'channel-id',
            'server_id' => 'team-id',
        ]);
        $channel->user = 'U' . \Str::random(8);
        $channel->username = 'Testing';
        self::expectException(SlackException::class);
        self::expectExceptionMessage(sprintf(
            'You must have already created an account on <%s|%s> and linked it '
                . 'to this server before you can register a channel to a '
                . 'specific system.',
            config('app.url'),
            config('app.name')
        ));
        $response = new RegisterResponse(
            sprintf('register %s', key(config('app.systems'))),
            RegisterResponse::HTTP_OK,
            [],
            $channel
        );
    }

    /**
     * Test registering a channel that's already registered.
     * @test
     */
    public function testChannelAlreadyRegistered(): void
    {
        self::expectException(SlackException::class);
        self::expectExceptionMessage(
            'This channel is already registered for "shadowrun5e"'
        );
        $response = new RegisterResponse(
            '',
            RegisterResponse::HTTP_OK,
            [],
            new Channel(['system' => 'shadowrun5e'])
        );
    }

    /**
     * Test registering a channel without specifying the system.
     * @test
     */
    public function testRegisterWithoutSystem(): void
    {
        self::expectException(SlackException::class);
        self::expectExceptionMessage(sprintf(
            'To register a channel, use `register [system]`, where system '
                . 'is a system code: %s',
            implode(', ', array_keys(config('app.systems')))
        ));
        $user = User::factory()->create();
        $channel = new Channel([
            'channel_id' => 'channel-id',
            'server_id' => 'team-id',
            'type' => ChatUser::TYPE_SLACK,
        ]);
        $channel->user = 'U' . \Str::random(8);
        $chatUser = ChatUser::factory([
            'remote_user_id' => $channel->user,
            'server_id' => 'team-id',
            'server_type' => ChatUser::TYPE_SLACK,
            'verified' => true,
        ])->create();
        $response = new RegisterResponse(
            'register',
            RegisterResponse::HTTP_OK,
            [],
            $channel
        );
    }

    /**
     * Test registering a channel to an invalid system.
     * @test
     */
    public function testRegisterInvalidSystem(): void
    {
        self::expectException(SlackException::class);
        self::expectExceptionMessage(sprintf(
            '"%s" is not a valid system code. Use `register [system]`, '
                . 'where system is: %s',
            'invalid',
            implode(', ', array_keys(config('app.systems')))
        ));
        $user = User::factory()->create();
        $channel = new Channel([
            'channel_id' => 'channel-id',
            'server_id' => 'team-id',
            'type' => ChatUser::TYPE_SLACK,
        ]);
        $channel->user = 'U' . \Str::random(8);
        $chatUser = ChatUser::factory([
            'remote_user_id' => $channel->user,
            'server_id' => 'team-id',
            'server_type' => ChatUser::TYPE_SLACK,
            'verified' => true,
        ])->create();
        $response = new RegisterResponse(
            'register invalid',
            RegisterResponse::HTTP_OK,
            [],
            $channel
        );
    }

    /**
     * Test registering a channel to a valid system after creating a Commlink
     * account and having all other required data.
     * @test
     */
    public function testRegister(): void
    {
        $user = User::factory()->create();
        $channel = new Channel([
            'channel_id' => 'channel-id',
            'server_id' => 'team-id',
            'type' => ChatUser::TYPE_SLACK,
        ]);
        $channel->user = 'U' . \Str::random(8);
        $chatUser = ChatUser::factory([
            'remote_user_id' => $channel->user,
            'server_id' => 'team-id',
            'server_type' => ChatUser::TYPE_SLACK,
            'verified' => true,
        ])->create();
        $response = new RegisterResponse(
            sprintf('register %s', key(config('app.systems'))),
            RegisterResponse::HTTP_OK,
            [],
            $channel
        );
        self::assertSame(key(config('app.systems')), $channel->system);
    }
}
