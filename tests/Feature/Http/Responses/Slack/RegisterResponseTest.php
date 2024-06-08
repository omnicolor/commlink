<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Responses\Slack;

use App\Exceptions\SlackException;
use App\Http\Responses\Slack\RegisterResponse;
use App\Models\Channel;
use App\Models\ChatUser;
use App\Models\User;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

#[Group('slack')]
#[Medium]
final class RegisterResponseTest extends TestCase
{
    /**
     * Test trying to register a channel without passing in the channel.
     */
    public function testChannelMissing(): void
    {
        self::expectException(SlackException::class);
        self::expectExceptionMessage('Channel is required');
        $response = new RegisterResponse();
    }

    /**
     * Test trying to register a system without creating a Commlink account.
     */
    public function testRegisterNoAccount(): void
    {
        $user = User::factory()->create();
        $channel = new Channel([
            'channel_id' => 'channel-id',
            'server_id' => 'team-id',
        ]);
        $channel->user = 'U' . Str::random(8);
        $channel->username = 'Testing';
        self::expectException(SlackException::class);
        self::expectExceptionMessage(\sprintf(
            'You must have already created an account on <%s|%s> and linked it '
                . 'to this server before you can register a channel to a '
                . 'specific system.',
            config('app.url'),
            config('app.name')
        ));
        $response = new RegisterResponse(
            content: \sprintf('register %s', \key(config('app.systems'))),
            channel: $channel,
        );
    }

    /**
     * Test registering a channel that's already registered.
     */
    public function testChannelAlreadyRegistered(): void
    {
        self::expectException(SlackException::class);
        self::expectExceptionMessage(
            'This channel is already registered for "shadowrun5e"'
        );
        $response = new RegisterResponse(
            channel: new Channel(['system' => 'shadowrun5e'])
        );
    }

    /**
     * Test registering a channel without specifying the system.
     */
    public function testRegisterWithoutSystem(): void
    {
        self::expectException(SlackException::class);
        self::expectExceptionMessage(\sprintf(
            'To register a channel, use `register [system]`, where system '
                . 'is a system code: %s',
            \implode(', ', \array_keys(config('app.systems')))
        ));
        $user = User::factory()->create();
        $channel = new Channel([
            'channel_id' => 'channel-id',
            'server_id' => 'team-id',
            'type' => ChatUser::TYPE_SLACK,
        ]);
        $channel->user = 'U' . Str::random(8);
        $chatUser = ChatUser::factory([
            'remote_user_id' => $channel->user,
            'server_id' => 'team-id',
            'server_type' => ChatUser::TYPE_SLACK,
            'verified' => true,
        ])->create();
        $response = new RegisterResponse(
            content: 'register',
            channel: $channel,
        );
    }

    /**
     * Test registering a channel to an invalid system.
     */
    public function testRegisterInvalidSystem(): void
    {
        self::expectException(SlackException::class);
        self::expectExceptionMessage(\sprintf(
            '"%s" is not a valid system code. Use `register [system]`, '
                . 'where system is: %s',
            'invalid',
            \implode(', ', \array_keys(config('app.systems')))
        ));
        $user = User::factory()->create();
        $channel = new Channel([
            'channel_id' => 'channel-id',
            'server_id' => 'team-id',
            'type' => ChatUser::TYPE_SLACK,
        ]);
        $channel->user = 'U' . Str::random(8);
        $chatUser = ChatUser::factory([
            'remote_user_id' => $channel->user,
            'server_id' => 'team-id',
            'server_type' => ChatUser::TYPE_SLACK,
            'verified' => true,
        ])->create();
        $response = new RegisterResponse(
            content: 'register invalid',
            channel: $channel,
        );
    }

    /**
     * Test registering a channel to a valid system after creating a Commlink
     * account and having all other required data.
     */
    public function testRegister(): void
    {
        $user = User::factory()->create();
        $channel = new Channel([
            'channel_id' => 'channel-id',
            'server_id' => 'team-id',
            'type' => ChatUser::TYPE_SLACK,
        ]);
        $channel->user = 'U' . Str::random(8);
        $chatUser = ChatUser::factory([
            'remote_user_id' => $channel->user,
            'server_id' => 'team-id',
            'server_type' => ChatUser::TYPE_SLACK,
            'verified' => true,
        ])->create();
        $response = new RegisterResponse(
            content: \sprintf('register %s', \key(config('app.systems'))),
            channel: $channel,
        );
        self::assertSame(\key(config('app.systems')), $channel->system);
    }
}
