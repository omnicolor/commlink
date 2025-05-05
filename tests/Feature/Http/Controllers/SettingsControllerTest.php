<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Models\Channel;
use App\Models\ChatUser;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use function sprintf;

#[Group('settings')]
#[Medium]
final class SettingsControllerTest extends TestCase
{
    protected const string API_DISCORD_GUILDS = 'discord.com/api/guilds/';
    protected const string API_DISCORD_USERS = 'discord.com/api/users/';
    protected const string API_SLACK_TEAMS = 'slack.com/api/auth.teams.list';
    protected const string API_SLACK_USERS = 'slack.com/api/users.info';

    /**
     * Test an authenticated user with no linked users.
     */
    public function testNoLinkedUsers(): void
    {
        $user = User::factory()->create();
        self::actingAs($user)
            ->get(route('settings.chat-users'))
            ->assertOk()
            ->assertSee('You don\'t have any linked chat users!', false);
    }

    /**
     * Test an authenticated user that has a linked user.
     */
    public function testWithLinkedUser(): void
    {
        $user = User::factory()->create();
        $server_id = 'T' . Str::random(10);
        $remote_user_id = 'U' . Str::random(10);
        $chat_user = ChatUser::factory()->create([
            'server_id' => $server_id,
            'server_type' => ChatUser::TYPE_SLACK,
            'remote_user_id' => $remote_user_id,
            'user_id' => $user->id,
            'verified' => false,
        ]);
        self::actingAs($user)
            ->get(route('settings.chat-users'))
            ->assertSee($chat_user->remote_user_id)
            ->assertSee($server_id)
            ->assertSee($remote_user_id)
            ->assertSee('/roll validate');
    }

    /**
     * Test creating a linked user without sending the required fields.
     */
    public function testLinkUserMissingData(): void
    {
        $user = User::factory()->create();
        self::actingAs($user)
            ->post(route('settings.link-user'), [])
            ->assertFound()
            ->assertSessionHasErrors();
        self::assertSame(
            ['The server-id field is required.'],
            session('errors')->get('server-id')
        );
        self::assertSame(
            ['The user-id field is required.'],
            session('errors')->get('user-id')
        );
    }

    /**
     * Test creating a chat user where the Slack requests for the team and user
     * names fail but the request otherwise succeeds.
     */
    public function testLinkUserSlackCallsFail(): void
    {
        $user = User::factory()->create();
        $server_id = 'T' . Str::random(10);
        $userId = 'U' . Str::random(10);
        Http::fake([
            self::API_SLACK_TEAMS => Http::response([
                'ok' => false,
                'error' => 'not_authed',
            ]),
            sprintf('%s?user=%s', self::API_SLACK_USERS, $userId) => Http::response([
                'ok' => false,
                'error' => 'not_authed',
            ]),
        ]);
        self::actingAs($user)
            ->post(
                route('settings.link-user'),
                [
                    'server-id' => $server_id,
                    'user-id' => $userId,
                ],
            )
            ->assertFound()
            ->assertSessionHasNoErrors();
        self::assertDatabaseHas(
            'chat_users',
            [
                'server_id' => $server_id,
                'server_name' => null,
                'server_type' => ChatUser::TYPE_SLACK,
                'remote_user_id' => $userId,
                'remote_user_name' => null,
                'user_id' => $user->id,
                'verified' => false,
            ],
        );
    }

    /**
     * Test trying to create a duplicate chat user.
     */
    public function testLinkDuplicateSlackUser(): void
    {
        $user = User::factory()->create();
        $server_id = 'T' . Str::random(10);
        $userId = 'U' . Str::random(10);
        ChatUser::factory()->create([
            'server_id' => $server_id,
            'server_type' => ChatUser::TYPE_SLACK,
            'remote_user_id' => $userId,
            'user_id' => $user->id,
        ]);
        self::actingAs($user)
            ->followingRedirects()
            ->post(
                route('settings.link-user'),
                [
                    'server-id' => $server_id,
                    'server-type' => ChatUser::TYPE_SLACK,
                    'user-id' => $userId,
                ],
            )
            ->assertSee('Slack user already registered.');
    }

    /**
     * Test trying to create a linked Discord user if the API calls fail.
     */
    public function testLinkDiscordUserAPICallsFail(): void
    {
        $user = User::factory()->create();
        $server_id = '1' . Str::random(10);
        $userId = Str::random(10);
        Http::fake([
            self::API_DISCORD_GUILDS . $server_id => Http::response([], Response::HTTP_BAD_REQUEST),
            self::API_DISCORD_USERS . $userId => Http::response([], Response::HTTP_NOT_FOUND),
        ]);
        self::actingAs($user)
            ->followingRedirects()
            ->post(
                route('settings.link-user'),
                [
                    'server-id' => $server_id,
                    'user-id' => $userId,
                ],
            )
            ->assertOk()
            ->assertSessionHasNoErrors();
        self::assertDatabaseHas(
            'chat_users',
            [
                'server_id' => $server_id,
                'server_name' => null,
                'server_type' => ChatUser::TYPE_DISCORD,
                'remote_user_id' => $userId,
                'remote_user_name' => null,
                'user_id' => $user->id,
                'verified' => false,
            ],
        );
    }

    /**
     * Test trying to create a linked Discord user.
     */
    public function testLinkDiscordUser(): void
    {
        $user = User::factory()->create();
        $server_id = '1' . Str::random(10);
        $userId = Str::random(10);
        Http::fake([
            self::API_DISCORD_GUILDS . $server_id => Http::response(
                ['name' => 'Discord Guild'],
                Response::HTTP_OK,
            ),
            self::API_DISCORD_USERS . $userId => Http::response(
                [
                    'username' => 'DiscordUser',
                    'discriminator' => '1234',
                ],
                Response::HTTP_OK,
            ),
        ]);
        self::actingAs($user)
            ->followingRedirects()
            ->post(
                route('settings.link-user'),
                [
                    'server-id' => $server_id,
                    'user-id' => $userId,
                ],
            )
            ->assertOk()
            ->assertSessionHasNoErrors();
        self::assertDatabaseHas(
            'chat_users',
            [
                'server_id' => $server_id,
                'server_name' => 'Discord Guild',
                'server_type' => ChatUser::TYPE_DISCORD,
                'remote_user_id' => $userId,
                'remote_user_name' => 'DiscordUser#1234',
                'user_id' => $user->id,
                'verified' => false,
            ],
        );
    }

    /**
     * Test trying to create a duplicate Discord chat user.
     */
    public function testLinkDuplicateDiscordUser(): void
    {
        $user = User::factory()->create();
        $server_id = '1' . Str::random(10);
        $userId = '2' . Str::random(10);
        ChatUser::factory()->create([
            'server_id' => $server_id,
            'server_type' => ChatUser::TYPE_DISCORD,
            'remote_user_id' => $userId,
            'user_id' => $user->id,
        ]);
        self::actingAs($user)
            ->followingRedirects()
            ->post(
                route('settings.link-user'),
                [
                    'server-id' => $server_id,
                    'server-type' => ChatUser::TYPE_DISCORD,
                    'user-id' => $userId,
                ],
            )
            ->assertSee('Discord user already registered.');
    }

    /**
     * Test trying to link a duplicate IRC user.
     */
    public function testLinkDuplicateIrcUser(): void
    {
        $user = User::factory()->create();
        $server_id = 'chat.freenode.net:6667';
        $userId = Str::random(10);
        ChatUser::factory()->create([
            'server_id' => $server_id,
            'server_type' => ChatUser::TYPE_IRC,
            'remote_user_id' => $userId,
            'user_id' => $user->id,
        ]);
        self::actingAs($user)
            ->followingRedirects()
            ->post(
                route('settings.link-user'),
                [
                    'server-id' => $server_id,
                    'server-type' => ChatUser::TYPE_IRC,
                    'user-id' => $userId,
                ],
            )
            ->assertSee('IRC user already registered.');
    }

    /**
     * Test linking a new IRC user.
     */
    public function testLinkIrcUser(): void
    {
        $user = User::factory()->create();
        $server_id = 'chat.freenode.net:6667';
        $userId = Str::random(10);
        self::actingAs($user)
            ->followingRedirects()
            ->post(
                route('settings.link-user'),
                [
                    'server-id' => $server_id,
                    'server-type' => ChatUser::TYPE_IRC,
                    'user-id' => $userId,
                ],
            )
            ->assertOk()
            ->assertSessionHasNoErrors();
        self::assertDatabaseHas(
            'chat_users',
            [
                'server_id' => $server_id,
                'server_name' => 'chat.freenode.net',
                'server_type' => ChatUser::TYPE_IRC,
                'remote_user_id' => $userId,
                'remote_user_name' => $userId,
                'user_id' => $user->id,
                'verified' => false,
            ],
        );
    }

    public function testChannelsEmpty(): void
    {
        self::actingAs(User::factory()->create())
            ->get(route('settings.channels'))
            ->assertSee('registered any channels');
    }

    public function testChannels(): void
    {
        $user = User::factory()->create();
        $channel = Channel::factory()->create(['registered_by' => $user->id]);
        self::actingAs($user)
            ->get(route('settings.channels'))
            ->assertSee($channel->channel_name);
    }

    public function testApiKeysEmpty(): void
    {
        self::actingAs(User::factory()->create())
            ->get(route('settings.api-keys'))
            ->assertSee('have any API keys!');
    }

    public function testApiKeysNotEmpty(): void
    {
        $user = User::factory()->create();
        $user->createToken('My API token', ['*'], null);
        self::actingAs($user)
            ->get(route('settings.api-keys'))
            ->assertSee('My API token');
    }
}
