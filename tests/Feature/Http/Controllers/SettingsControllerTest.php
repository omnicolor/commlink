<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Models\ChatUser;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

/**
 * Tests for the settings controller.
 * @group controllers
 * @medium
 */
final class SettingsControllerTest extends \Tests\TestCase
{
    protected const API_DISCORD_GUILDS = 'discord.com/api/guilds/';
    protected const API_DISCORD_USERS = 'discord.com/api/users/';
    protected const API_SLACK_TEAMS = 'slack.com/api/auth.teams.list';
    protected const API_SLACK_USERS = 'slack.com/api/users.info';

    /**
     * Test an unauthenticated request.
     * @test
     */
    public function testUnauthenticated(): void
    {
        $this->get('/settings')->assertRedirect('/login');
    }

    /**
     * Test an authenticated user with no linked users.
     * @test
     */
    public function testNoLinkedUsers(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $this->actingAs($user)
            ->get('/settings')
            ->assertOk()
            ->assertSee('You don\'t have any linked chat users!', false);
    }

    /**
     * Test an authenticated user with a linked user.
     * @test
     */
    public function testWithLinkedUser(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $serverId = 'T' . \Str::random(10);
        $remoteUserId = 'U' . \Str::random(10);
        ChatUser::factory()->create([
            'server_id' => $serverId,
            'server_type' => ChatUser::TYPE_SLACK,
            'remote_user_id' => $remoteUserId,
            'user_id' => $user->id,
            'verified' => false,
        ]);
        $this->actingAs($user)
            ->get('/settings')
            ->assertDontSee('You don\'t have any linked chat users!', false)
            ->assertSee($serverId)
            ->assertSee($remoteUserId)
            ->assertSee('/roll validateUser');
    }

    /**
     * Test creating a linked user without sending the required fields.
     * @test
     */
    public function testLinkUserMissingData(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $this->actingAs($user)
            ->post('/settings/link-user', [])
            ->assertStatus(Response::HTTP_FOUND)
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
     * @test
     */
    public function testLinkUserSlackCallsFail(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $serverId = 'T' . \Str::random(10);
        $userId = 'U' . \Str::random(10);
        Http::fake([
            self::API_SLACK_TEAMS => Http::response([
                'ok' => false,
                'error' => 'not_authed',
            ]),
            \sprintf('%s?user=%s', self::API_SLACK_USERS, $userId) => Http::response([
                'ok' => false,
                'error' => 'not_authed',
            ]),
        ]);
        $this->actingAs($user)
            ->post(
                '/settings/link-user',
                [
                    'server-id' => $serverId,
                    'user-id' => $userId,
                ]
            )
            ->assertStatus(Response::HTTP_FOUND)
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas(
            'chat_users',
            [
                'server_id' => $serverId,
                'server_name' => null,
                'server_type' => ChatUser::TYPE_SLACK,
                'remote_user_id' => $userId,
                'remote_user_name' => null,
                'user_id' => $user->id,
                'verified' => false,
            ]
        );
    }

    /**
     * Test trying to create a duplicate chat user.
     * @test
     */
    public function testLinkDuplicateSlackUser(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $serverId = 'T' . \Str::random(10);
        $userId = 'U' . \Str::random(10);
        ChatUser::factory()->create([
            'server_id' => $serverId,
            'server_type' => ChatUser::TYPE_SLACK,
            'remote_user_id' => $userId,
            'user_id' => $user->id,
        ]);
        $this->actingAs($user)
            ->followingRedirects()
            ->post(
                '/settings/link-user',
                [
                    'server-id' => $serverId,
                    'server-type' => ChatUser::TYPE_SLACK,
                    'user-id' => $userId,
                ]
            )
            ->assertSee('User already registered.');
    }

    /**
     * Test trying to create a linked Discord user.
     * @test
     */
    public function testLinkDiscordUserAPICallsFail(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $serverId = '1' . \Str::random(10);
        $userId = \Str::random(10);
        Http::fake([
            self::API_DISCORD_GUILDS . $serverId => Http::response([], Response::HTTP_BAD_REQUEST),
            self::API_DISCORD_USERS . $userId => Http::response([], Response::HTTP_NOT_FOUND),
        ]);
        $response = $this->actingAs($user)
            ->followingRedirects()
            ->post(
                '/settings/link-user',
                [
                    'server-id' => $serverId,
                    'user-id' => $userId,
                ]
            )
            ->assertOk()
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas(
            'chat_users',
            [
                'server_id' => $serverId,
                'server_name' => null,
                'server_type' => ChatUser::TYPE_DISCORD,
                'remote_user_id' => $userId,
                'remote_user_name' => null,
                'user_id' => $user->id,
                'verified' => false,
            ]
        );
    }
}
