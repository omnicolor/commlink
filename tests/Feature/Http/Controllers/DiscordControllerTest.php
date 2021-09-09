<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Models\Traits\InteractsWithDiscord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

/**
 * @medium
 */
final class DiscordControllerTest extends \Tests\TestCase
{
    use InteractsWithDiscord;
    use RefreshDatabase;

    /**
     * Test getting a redirect to /discord without being logged in.
     * @test
     */
    public function testGetNotLoggedIn(): void
    {
        $this->get(route('discord.view'))->assertRedirect(route('login'));
    }

    /**
     * Test getting a redirect to /discord without a code.
     * @test
     */
    public function testGetNoCode(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $this->actingAs($user)->get(route('discord.view'))
            ->assertRedirect(route('settings'))
            ->assertSessionHasErrors([
                'error' => 'Discord login failed, no Oauth code supplied',
            ]);
    }

    /**
     * Test getting a redirect to /discord with an improper length code.
     * @test
     */
    public function testGetWrongLengthCode(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $this->actingAs($user)
            ->get(route('discord.view', ['code' => '1']))
            ->assertRedirect(route('settings'))
            ->assertSessionHasErrors([
                'error' => 'Discord login failed, invalid Oauth code',
            ]);
    }

    /**
     * Test trying to login with Discord, but the Discord API fails.
     * @test
     */
    public function testGetDiscordAPIFails(): void
    {
        Http::fake([
            '*' => Http::response('', Response::HTTP_BAD_REQUEST),
        ]);
        /** @var User */
        $user = User::factory()->create();
        $this->actingAs($user)
            ->get(route('discord.view', ['code' => \Str::random(30)]))
            ->assertRedirect(route('settings'))
            ->assertSessionHasErrors([
                'error' => \sprintf(
                    'Request to Discord failed. Please <a href="%s">try again</a>.',
                    $this->getDiscordOauthURL(),
                ),
            ]);
    }

    /**
     * Test login with Discord.
     * @test
     */
    public function testGetDiscord(): void
    {
        Http::fake([
            'https://discord.com/api/oauth2/token' => Http::response(
                ['access_token' => 'token-for-access'],
                Response::HTTP_OK
            ),
            'https://discord.com/api/users/@me' => Http::response(
                [
                    'avatar' => 'abc123',
                    'discriminator' => '1234',
                    'id' => '987654321',
                    'username' => 'bob-king',
                ],
                Response::HTTP_OK
            ),
            'https://discord.com/api/users/@me/guilds' => Http::response(
                [
                    // Guild with an icon.
                    [
                        'icon' => 'zxpknobb',
                        'name' => 'Guild War Z',
                        'id' => '2222222',
                    ],
                    // Guild with no icon.
                    [
                        'icon' => null,
                        'name' => 'Iconography',
                        'id' => '3333333',
                    ],
                ],
                Response::HTTP_OK
            ),
            '*' => Http::response('', Response::HTTP_BAD_REQUEST),
        ]);
        /** @var User */
        $user = User::factory()->create();
        $this->actingAs($user)
            ->get(route('discord.view', ['code' => \Str::random(30)]))
            ->assertOk()
            ->assertSee('bob-king#1234')
            ->assertSee('Iconography');
    }

    /**
     * Test a request to link a user without selecting a guild.
     * @test
     */
    public function testSaveWithNoGuilds(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $this->actingAs($user)
            ->post(route('discord.save'))
            ->assertRedirect(route('settings'))
            ->assertSessionHasErrors([
                'error' => 'No guilds selected.',
            ]);
    }

    /**
     * Test a request to link a user that wasn't in the list of guilds.
     * @test
     */
    public function testSaveWithInvalidGuild(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $this->actingAs($user)
            ->post(route('discord.save'), ['guilds' => ['0']])
            ->assertRedirect(route('settings'))
            ->assertSessionHasErrors([
                'error' => 'An invalid Guild ID was found.',
            ]);
    }

    /**
     * Test a request to link a Discord user.
     * @test
     */
    public function testSaveDiscordUser(): void
    {
        session([
            'guilds' => [
                // Guild with an icon.
                [
                    'icon' => 'zxpknobb',
                    'name' => 'Guild War Z',
                    'snowflake' => '2222222',
                ],
                // Guild with no icon.
                [
                    'icon' => null,
                    'name' => 'Iconography',
                    'snowflake' => '3333333',
                ],
            ],
            'discordUser' => [
                'avatar' => 'abc123',
                'discriminator' => '1234',
                'snowflake' => \Str::random(12),
                'username' => 'bob-king',
            ],
        ]);

        /** @var User */
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('discord.save'), ['guilds' => ['3333333']])
            ->assertRedirect(route('settings'))
            ->assertSessionHasNoErrors();
    }
}
