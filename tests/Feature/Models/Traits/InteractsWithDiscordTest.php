<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Traits;

use App\Models\Traits\InteractsWithDiscord;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\MockObject\MockObject;
use RuntimeException;
use Tests\TestCase;

#[Group('discord')]
#[Small]
final class InteractsWithDiscordTest extends TestCase
{
    /**
     * Subject under test.
     * @var MockObject
     */
    protected $mock;

    /**
     * Set up the subject under test.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->mock = $this->getMockForTrait(InteractsWithDiscord::class);
    }

    /**
     * Test creating a Discord webhook.
     */
    public function testCreateDiscordWebHook(): void
    {
        Http::fake([
            'https://discord.com/api/channels/456/webhooks' => Http::response(
                [
                    'id' => 987,
                    'token' => 'abc',
                ],
                Response::HTTP_OK,
            ),
            '*' => Http::response('', Response::HTTP_BAD_REQUEST),
        ]);
        self::assertSame(
            'https://discord.com/api/webhooks/987/abc',
            // @phpstan-ignore-next-line
            $this->mock->createDiscordWebhook('456')
        );
    }

    /**
     * Test creating a Discord webhook if the call fails.
     */
    public function testCreateDiscordWebHookFails(): void
    {
        Http::fake([
            '*' => Http::response('', Response::HTTP_BAD_REQUEST),
        ]);
        // @phpstan-ignore-next-line
        self::assertNull($this->mock->createDiscordWebhook('123'));
    }

    /**
     * Test getting a Discord Channel name.
     */
    public function testGetDiscordChannelName(): void
    {
        Http::fake([
            'https://discord.com/api/channels/765' => Http::response(
                [
                    'name' => '#foo-channel',
                ],
                Response::HTTP_OK
            ),
            '*' => Http::response('', Response::HTTP_BAD_REQUEST),
        ]);
        self::assertSame(
            '#foo-channel',
            // @phpstan-ignore-next-line
            $this->mock->getDiscordChannelName('765')
        );
    }

    /**
     * Test getting a Discord channel name if the call fails.
     */
    public function testGetDiscordChannelNameFail(): void
    {
        Http::fake([
            '*' => Http::response('', Response::HTTP_BAD_REQUEST),
        ]);
        // @phpstan-ignore-next-line
        self::assertNull($this->mock->getDiscordChannelName('765'));

        Http::assertSent(function (Request $request): bool {
            return 'https://discord.com/api/channels/765' === $request->url();
        });
    }

    /**
     * Test getting a Discord username with a failing call.
     */
    public function testGetDiscordUsernameFail(): void
    {
        Http::fake([
            '*' => Http::response('', Response::HTTP_BAD_REQUEST),
        ]);
        // @phpstan-ignore-next-line
        self::assertNull($this->mock->getDiscordUserName('42'));

        Http::assertSent(function (Request $request): bool {
            return 'https://discord.com/api/users/42' === $request->url();
        });
    }

    /**
     * Test getting a Discord username.
     */
    public function testGetDiscordUsername(): void
    {
        Http::fake([
            'https://discord.com/api/users/99'  => Http::response(
                [
                    'username' => 'user',
                    'discriminator' => '1234',
                ],
                Response::HTTP_OK
            ),
            '*' => Http::response('', Response::HTTP_BAD_REQUEST),
        ]);
        // @phpstan-ignore-next-line
        self::assertSame('user#1234', $this->mock->getDiscordUserName('99'));
    }

    /**
     * Test getting a Discord server name with a failing call.
     */
    public function testGetDiscordServerNameFails(): void
    {
        Http::fake([
            '*' => Http::response('', Response::HTTP_BAD_REQUEST),
        ]);
        // @phpstan-ignore-next-line
        self::assertNull($this->mock->getDiscordServerName('13'));
        Http::assertSent(function (Request $request): bool {
            return 'https://discord.com/api/guilds/13' === $request->url();
        });
    }

    /**
     * Test getting a Discord server name.
     */
    public function testGetDiscordServerName(): void
    {
        Http::fake([
            'https://discord.com/api/guilds/234' => Http::response(
                [
                    'name' => 'Burning Edge',
                ],
                Response::HTTP_OK
            ),
            '*' => Http::response('', Response::HTTP_BAD_REQUEST),
        ]);
        self::assertSame(
            'Burning Edge',
            // @phpstan-ignore-next-line
            $this->mock->getDiscordServerName('234')
        );
    }

    /**
     * Test getting a Discord access token with a failing call.
     */
    public function testGetDiscordAccessTokenFail(): void
    {
        Http::fake([
            '*' => Http::response('Nope', Response::HTTP_BAD_REQUEST),
        ]);
        try {
            // @phpstan-ignore-next-line
            $this->mock->getDiscordAccessToken('13');
        } catch (RuntimeException $ex) {
            self::assertSame('Nope', $ex->getMessage());
            Http::assertSent(function (Request $request): bool {
                return 'https://discord.com/api/oauth2/token' === $request->url();
            });
            return;
        }
        self::fail('No exception thrown');
    }

    /**
     * Test getting a Discord access token.
     */
    public function testGetDiscordAccessToken(): void
    {
        Http::fake([
            'https://discord.com/api/oauth2/token' => Http::response(
                [
                    'access_token' => 'Jame-Bond',
                ],
                Response::HTTP_OK
            ),
            '*' => Http::response('', Response::HTTP_BAD_REQUEST),
        ]);
        self::assertSame(
            // There's only one Jame.
            'Jame-Bond',
            // @phpstan-ignore-next-line
            $this->mock->getDiscordAccessToken('007')
        );
        Http::assertSent(function (Request $request): bool {
            return 'https://discord.com/api/oauth2/token' === $request->url();
        });
    }

    /**
     * Test getting a Discord user's record if the call fails.
     */
    public function testGetDiscordUserFail(): void
    {
        Http::fake([
            '*' => Http::response('forget it', Response::HTTP_BAD_REQUEST),
        ]);
        try {
            // @phpstan-ignore-next-line
            $this->mock->getDiscordUser('13');
        } catch (RuntimeException $ex) {
            self::assertSame('forget it', $ex->getMessage());
            Http::assertSent(function (Request $request): bool {
                return 'https://discord.com/api/users/@me' === $request->url();
            });
            return;
        }
        self::fail('No exception thrown');
    }

    /**
     * Test getting a Discord user's record that doesn't have an avatar.
     */
    public function testGetDiscordUserNoAvatar(): void
    {
        Http::fake([
            'https://discord.com/api/users/@me' => Http::response(
                [
                    'avatar' => null,
                    'discriminator' => '1234',
                    'id' => '987654321',
                    'username' => 'bob-king',
                ],
                Response::HTTP_OK
            ),
            '*' => Http::response('', Response::HTTP_BAD_REQUEST),
        ]);
        $expected = [
            'avatar' => null,
            'discriminator' => '1234',
            'snowflake' => '987654321',
            'username' => 'bob-king',
        ];
        // @phpstan-ignore-next-line
        self::assertSame($expected, $this->mock->getDiscordUser('user-token'));
        Http::assertSent(function (Request $request): bool {
            return 'https://discord.com/api/users/@me' === $request->url();
        });
    }

    /**
     * Test getting a Discord user's record that has an avatar.
     */
    public function testGetDiscordUserAvatar(): void
    {
        Http::fake([
            'https://discord.com/api/users/@me' => Http::response(
                [
                    'avatar' => 'abc123',
                    'discriminator' => '1234',
                    'id' => '987654321',
                    'username' => 'bob-king',
                ],
                Response::HTTP_OK
            ),
            '*' => Http::response('', Response::HTTP_BAD_REQUEST),
        ]);
        $expected = [
            'avatar' => 'https://cdn.discordapp.com/avatars/987654321/abc123.png',
            'discriminator' => '1234',
            'snowflake' => '987654321',
            'username' => 'bob-king',
        ];
        // @phpstan-ignore-next-line
        self::assertSame($expected, $this->mock->getDiscordUser('user-token'));
        Http::assertSent(function (Request $request): bool {
            return 'https://discord.com/api/users/@me' === $request->url();
        });
    }

    /**
     * Test getting a Discord user's guilds if the call fails.
     */
    public function testGetDiscordGuildsFail(): void
    {
        Http::fake([
            '*' => Http::response('broken', Response::HTTP_BAD_REQUEST),
        ]);
        try {
            // @phpstan-ignore-next-line
            $this->mock->getDiscordGuilds('13');
        } catch (RuntimeException $ex) {
            self::assertSame('broken', $ex->getMessage());
            Http::assertSent(function (Request $request): bool {
                return 'https://discord.com/api/users/@me/guilds' === $request->url();
            });
            return;
        }
        self::fail('No exception thrown');
    }

    /**
     * Test getting a Discord user's guilds if they have none.
     */
    public function testGetDiscordGuildsNone(): void
    {
        Http::fake([
            'https://discord.com/api/users/@me/guilds' => Http::response(
                [],
                Response::HTTP_OK
            ),
            '*' => Http::response('', Response::HTTP_BAD_REQUEST),
        ]);
        // @phpstan-ignore-next-line
        self::assertCount(0, $this->mock->getDiscordGuilds('user-token'));
        Http::assertSent(function (Request $request): bool {
            return 'https://discord.com/api/users/@me/guilds' === $request->url();
        });
    }

    /**
     * Test getting a Discord user's guilds.
     */
    public function testGetDiscordGuilds(): void
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
        // @phpstan-ignore-next-line
        $guilds = $this->mock->getDiscordGuilds('user-token');
        self::assertCount(2, $guilds);
        self::assertSame(
            [
                'icon' => 'https://cdn.discordapp.com/icons/2222222/zxpknobb.png',
                'name' => 'Guild War Z',
                'snowflake' => '2222222',
            ],
            $guilds[0]
        );
        self::assertSame(
            [
                'icon' => null,
                'name' => 'Iconography',
                'snowflake' => '3333333',
            ],
            $guilds[1]
        );
    }

    /**
     * Test getting the Discord Oauth2 URL for authorization.
     */
    public function testGetOauthUrl(): void
    {
        $expected = sprintf(
            'https://discord.com/api/oauth2/authorize?client_id=%s'
                . '&redirect_uri=%s&response_type=code&scope=identify+guilds',
            config('services.discord.client_id'),
            urlencode(config('services.discord.redirect')),
        );
        // @phpstan-ignore-next-line
        self::assertSame($expected, $this->mock->getDiscordOauthURL());
    }
}
