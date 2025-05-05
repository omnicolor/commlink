<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Responses\Slack;

use App\Http\Responses\Slack\RegisterResponse;
use App\Models\Channel;
use App\Models\ChatUser;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Omnicolor\Slack\Exceptions\SlackException;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use function array_keys;
use function config;
use function implode;
use function key;
use function sprintf;

#[Group('slack')]
#[Medium]
final class RegisterResponseTest extends TestCase
{
    public function testChannelMissing(): void
    {
        self::expectException(SlackException::class);
        self::expectExceptionMessage('Channel is required');
        new RegisterResponse();
    }

    /**
     * Test trying to register a system without creating a Commlink account.
     */
    public function testRegisterNoAccount(): void
    {
        $channel = new Channel([
            'channel_id' => 'channel-id',
            'server_id' => 'team-id',
        ]);
        $channel->user = 'U' . Str::random(8);
        $channel->username = 'Testing';

        self::expectException(SlackException::class);
        self::expectExceptionMessage(sprintf(
            'You must have already created an account on <%s|%s> and linked it '
                . 'to this server before you can register a channel to a '
                . 'specific system.',
            config('app.url'),
            config('app.name')
        ));
        new RegisterResponse(
            content: sprintf('register %s', key(config('commlink.systems'))),
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
        new RegisterResponse(
            channel: new Channel(['system' => 'shadowrun5e'])
        );
    }

    /**
     * Test registering a channel without specifying the system.
     */
    public function testRegisterWithoutSystem(): void
    {
        $channel = new Channel([
            'channel_id' => 'channel-id',
            'server_id' => 'team-id',
            'type' => ChatUser::TYPE_SLACK,
        ]);
        $channel->user = 'U' . Str::random(8);
        ChatUser::factory([
            'remote_user_id' => $channel->user,
            'server_id' => 'team-id',
            'server_type' => ChatUser::TYPE_SLACK,
            'verified' => true,
        ])->create();

        self::expectException(SlackException::class);
        self::expectExceptionMessage(sprintf(
            'To register a channel, use `register [system]`, where system '
                . 'is a system code: %s',
            implode(', ', array_keys(config('commlink.systems')))
        ));
        new RegisterResponse(
            content: 'register',
            channel: $channel,
        );
    }

    /**
     * Test registering a channel to an invalid system.
     */
    public function testRegisterInvalidSystem(): void
    {
        $channel = new Channel([
            'channel_id' => 'channel-id',
            'server_id' => 'team-id',
            'type' => ChatUser::TYPE_SLACK,
        ]);
        $channel->user = 'U' . Str::random(8);
        ChatUser::factory([
            'remote_user_id' => $channel->user,
            'server_id' => 'team-id',
            'server_type' => ChatUser::TYPE_SLACK,
            'verified' => true,
        ])->create();

        self::expectException(SlackException::class);
        self::expectExceptionMessage(sprintf(
            '"%s" is not a valid system code. Use `register [system]`, '
                . 'where system is: %s',
            'invalid',
            implode(', ', array_keys(config('commlink.systems')))
        ));
        new RegisterResponse(
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
        $channels = [
            'ok' => true,
            'channel' => [
                'name' => 'channel name',
            ],
        ];
        $channels_response = Http::response($channels, Response::HTTP_OK);
        $teams = [
            'ok' => true,
            'teams' => [
                [
                    'id' => 'team-id',
                    'name' => 'foo',
                ],
            ],
        ];

        Http::preventStrayRequests();
        Http::fake([
            'https://slack.com/api/auth.teams.list' => Http::response($teams, Response::HTTP_OK),
            'https://slack.com/api/conversations.info?channel=channel-id' => $channels_response,
        ]);

        $channel = new Channel([
            'channel_id' => 'channel-id',
            'server_id' => 'team-id',
            'type' => ChatUser::TYPE_SLACK,
        ]);
        $channel->user = 'U' . Str::random(8);
        ChatUser::factory([
            'remote_user_id' => $channel->user,
            'server_id' => 'team-id',
            'server_type' => ChatUser::TYPE_SLACK,
            'verified' => true,
        ])->create();
        new RegisterResponse(
            content: sprintf('register %s', key(config('commlink.systems'))),
            channel: $channel,
        );
        self::assertSame(key(config('commlink.systems')), $channel->system);
    }
}
