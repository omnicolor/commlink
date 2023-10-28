<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Models\ChatUser;
use App\Models\Traits\InteractsWithDiscord;
use App\Models\User;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Tests\TestCase;

use function sprintf;

/**
 * @medium
 */
final class DiscordControllerTest extends TestCase
{
    use InteractsWithDiscord;
    use RefreshDatabase;
    use WithFaker;

    /**
     * Test getting a redirect to /discord without being logged in.
     * @test
     */
    public function testGetNotLoggedIn(): void
    {
        self::get(route('discord.view'))->assertRedirect(route('login'));
    }

    /**
     * Test getting a redirect to /discord without a code.
     * @test
     */
    public function testGetNoCode(): void
    {
        /** @var User */
        $user = User::factory()->create();
        self::actingAs($user)->get(route('discord.view'))
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
        self::actingAs($user)
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
        self::actingAs($user)
            ->get(route('discord.view', ['code' => Str::random(30)]))
            ->assertRedirect(route('settings'))
            ->assertSessionHasErrors([
                'error' => sprintf(
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
        self::actingAs($user)
            ->get(route('discord.view', ['code' => Str::random(30)]))
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
        self::actingAs($user)
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
        self::actingAs($user)
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
                'snowflake' => Str::random(12),
                'username' => 'bob-king',
            ],
        ]);

        /** @var User */
        $user = User::factory()->create();

        self::actingAs($user)
            ->post(route('discord.save'), ['guilds' => ['3333333']])
            ->assertRedirect(route('settings'))
            ->assertSessionHasNoErrors();
    }

    /**
     * Test trying to authenticate through Discord.
     * @test
     */
    public function testAuthThroughDiscord(): void
    {
        self::get('/discord/auth')
            ->assertRedirectContains('https://discord.com/api/oauth2/authorize?');
    }

    /**
     * Test a successful login through Discord with an existing user.
     * @test
     */
    public function testLoginThroughDiscordExistingUser(): void
    {
        /** @var User */
        $user = User::factory()->create();

        Socialite::shouldReceive('driver->user')
            ->andReturn((object)[
                'email' => $user->email,
                'token' => $this->faker->md5(),
                'avatar' => $this->faker->md5(),
                'id' => $this->faker->word(),
                'name' => $this->faker->name(),
                'user' => [
                    'discriminator' => $this->faker->randomNumber(4, true),
                ],
            ]);
        self::get('discord/callback')->assertRedirect('/discord');
        self::assertAuthenticatedAs($user);
    }

    /**
     * Test a successful login through Discord with a new user.
     * @test
     */
    public function testLoginThroughDiscordNewUser(): void
    {
        // Find an email that hasn't been used yet.
        do {
            $email = $this->faker->email();
        } while (null !== User::where('email', $email)->first());

        $name = $this->faker->name();
        Socialite::shouldReceive('driver->user')
            ->andReturn((object)[
                'email' => $email,
                'token' => $this->faker->md5(),
                'avatar' => $this->faker->md5(),
                'id' => $this->faker->word(),
                'name' => $name,
                'user' => [
                    'discriminator' => $this->faker->randomNumber(4, true),
                ],
            ]);
        self::get('discord/callback')->assertRedirect('/discord');

        /** @user */
        $user = User::where('email', $email)->first();
        self::assertNotNull($user);
        self::assertSame($name, $user->name);
        self::assertSame('reset me', $user->password);
        self::assertAuthenticatedAs($user);
    }

    /**
     * Test logging in with Discord with new Guilds.
     * @test
     */
    public function testLoginThroughDiscordNewGuilds(): void
    {
        Http::fake([
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

        $username = $this->faker->word();
        $discriminator = $this->faker->randomNumber(4, true);
        session([
            'discordUser' => [
                'token' => 'discord-token',
                'avatar' => $this->faker->md5(),
                'snowflake' => $this->faker->md5(),
                'username' => $username,
                'discriminator' => $discriminator,
            ],
        ]);
        self::actingAs($user)
            ->get(route('discord.view'))
            ->assertOk()
            ->assertSee($username . '#' . $discriminator)
            ->assertSee('Iconography');
    }

    /**
     * Test logging in with Discord if the user has already registered all of
     * their guilds.
     * @test
     */
    public function testLoginThroughDiscordNoNewGuilds(): void
    {
        Http::fake([
            'https://discord.com/api/users/@me/guilds' => Http::response(
                [
                    // Guild with an icon.
                    [
                        'icon' => 'zxpknobb',
                        'name' => 'Guild War Z',
                        'id' => '2222222',
                    ],
                ],
                Response::HTTP_OK
            ),
            '*' => Http::response('', Response::HTTP_BAD_REQUEST),
        ]);

        /** @var User */
        $user = User::factory()->create();

        $username = $this->faker->word();
        $discriminator = $this->faker->randomNumber(4, true);
        $snowflake = $this->faker->md5();
        session([
            'discordUser' => [
                'token' => 'discord-token',
                'avatar' => $this->faker->md5(),
                'snowflake' => $snowflake,
                'username' => $username,
                'discriminator' => $discriminator,
            ],
        ]);

        // Register the guild with Commlink.
        ChatUser::factory()->create([
            'user_id' => $user,
            'remote_user_id' => $snowflake,
            'server_id' => '2222222',
            'server_type' => ChatUser::TYPE_DISCORD,
        ]);

        self::actingAs($user)
            ->get(route('discord.view'))
            ->assertRedirect(route('dashboard'));
    }

    /**
     * Test a successful login through Discord with an existing user, but the
     * call back to Discord for the guild list fails.
     * @test
     */
    public function testLoginThroughDiscordGuildRequestFails(): void
    {
        Http::fake([
            '*' => Http::response('', Response::HTTP_BAD_REQUEST),
        ]);

        /** @var User */
        $user = User::factory()->create();

        session([
            'discordUser' => [
                'token' => 'discord-token',
                'avatar' => $this->faker->md5(),
                'snowflake' => $this->faker->md5(),
                'username' => $this->faker->word(),
                'discriminator' => $this->faker->randomNumber(4, true),
            ],
        ]);

        self::actingAs($user)
            ->get(route('discord.view'))
            ->assertRedirect(route('settings'))
            ->assertSessionHasErrors([
                'error' => sprintf(
                    'Request to Discord failed. Please <a href="%s">try again</a>.',
                    $this->getDiscordOauthURL(),
                ),
            ]);
    }

    public function testSocialiteFailingWithInvalidState(): void
    {
        Socialite::shouldReceive('driver->user')
            ->andThrow(InvalidStateException::class);
        self::get('discord/callback')->assertSessionHasErrors();
    }

    public function testSocialiteFailingWithClientException(): void
    {
        Socialite::shouldReceive('driver->user')
            ->andThrow(new ClientException(
                'Error communicating with server',
                new GuzzleRequest('GET', 'test'),
                new GuzzleResponse(),
            ));
        self::get('discord/callback')->assertSessionHasErrors();
    }
}
