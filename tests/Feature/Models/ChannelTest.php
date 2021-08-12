<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\Channel;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

/**
 * Tests for the channel model class.
 * @group discord
 * @group models
 * @group slack
 * @medium
 */
final class ChannelTest extends \Tests\TestCase
{
    /**
     * URL that Slack connections will go to.
     * @var string
     */
    protected const API_SLACK_TEAMS = 'slack.com/api/auth.teams.list';

    /**
     * URL that Discord connections will go to.
     * @var string
     */
    protected const API_DISCORD_GUILDS = 'https://discord.com/api/guilds/';

    /**
     * Faker instance.
     * @var \Faker\Generator
     */
    protected static \Faker\Generator $faker;

    /**
     * Set up the faker instance.
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$faker = \Faker\Factory::create();
    }

    /**
     * Test getting the server's name if we've already retrieved it.
     * @small
     * @test
     */
    public function testGetServerNameAlreadyHave(): void
    {
        Http::fake();
        Http::assertNothingSent();
        $channel = new Channel(['server_name' => 'Test instance']);
        self::assertSame('Test instance', $channel->server_name);
    }

    /**
     * Test getting the server's name for a Slack instance that hasn't been
     * saved.
     * @medium
     * @test
     */
    public function testGetServerNameNewSlackInstance(): void
    {
        $channel = new Channel([
            'server_id' => 'T' . \Str::random(10),
            'type' => Channel::TYPE_SLACK,
        ]);
        self::assertDatabaseMissing(
            'channels',
            [
                'server_id' => $channel->server_id,
                'type' => Channel::TYPE_SLACK,
            ]
        );
        $name = \Str::random(20);
        Http::fake([
            self::API_SLACK_TEAMS => Http::response([
                'ok' => true,
                'teams' => [
                    [
                        'id' => $channel->server_id,
                        'name' => $name,
                    ],
                ],
            ]),
        ]);
        self::assertSame($name, $channel->server_name);
        self::assertDatabaseMissing(
            'channels',
            [
                'server_id' => $channel->server_id,
                'type' => Channel::TYPE_SLACK,
            ]
        );
    }

    /**
     * Test that getting a server's name for the first time on a saved instance
     * updates the database.
     * @medium
     * @test
     */
    public function testGetServerNameSlackInstance(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $channel = new Channel([
            'channel_id' => 'C' . \Str::random(10),
            'registered_by' => $user->id,
            'server_id' => 'T' . \Str::random(10),
            'system' => self::$faker->randomElement(\array_keys(config('app.systems'))),
            'type' => Channel::TYPE_SLACK,
        ]);
        $channel->save();
        $name = \Str::random(20);
        self::assertDatabaseHas(
            'channels',
            [
                'id' => $channel->id,
                'server_id' => $channel->server_id,
                'server_name' => null,
                'type' => Channel::TYPE_SLACK,
            ]
        );
        Http::fake([
            self::API_SLACK_TEAMS => Http::response([
                'ok' => true,
                'teams' => [
                    [
                        'id' => $channel->server_id,
                        'name' => $name,
                    ],
                ],
            ]),
        ]);
        self::assertSame($name, $channel->server_name);
        self::assertDatabaseHas(
            'channels',
            [
                'id' => $channel->id,
                'server_id' => $channel->server_id,
                'server_name' => $name,
                'type' => Channel::TYPE_SLACK,
            ]
        );
    }

    /**
     * Test getting the server's name for a Discord instance that hasn't been
     * saved.
     * @medium
     * @test
     */
    public function testGetServerNameNewDiscordInstance(): void
    {
        $channel = new Channel([
            'server_id' => '1' . \Str::random(10),
            'type' => Channel::TYPE_DISCORD,
        ]);
        self::assertDatabaseMissing(
            'channels',
            [
                'server_id' => $channel->server_id,
                'type' => Channel::TYPE_DISCORD,
            ]
        );
        $name = \Str::random(20);
        Http::fake([
            self::API_DISCORD_GUILDS . $channel->server_id => Http::response(
                ['name' => $name],
                Response::HTTP_OK
            ),
        ]);
        self::assertSame($name, $channel->server_name);
        self::assertDatabaseMissing(
            'channels',
            [
                'server_id' => $channel->server_id,
                'type' => Channel::TYPE_DISCORD,
            ]
        );
    }

    /**
     * Test that getting a server's name for the first time on a saved instance
     * updates the database.
     * @medium
     * @test
     */
    public function testGetServerNameDiscordInstance(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $channel = new Channel([
            'channel_id' => '2' . \Str::random(10),
            'registered_by' => $user->id,
            'server_id' => '1' . \Str::random(10),
            'system' => self::$faker->randomElement(\array_keys(config('app.systems'))),
            'type' => Channel::TYPE_DISCORD,
        ]);
        $channel->save();
        $name = \Str::random(20);
        self::assertDatabaseHas(
            'channels',
            [
                'id' => $channel->id,
                'server_id' => $channel->server_id,
                'server_name' => null,
                'type' => Channel::TYPE_DISCORD,
            ]
        );
        Http::fake([
            self::API_DISCORD_GUILDS . $channel->server_id => Http::response(
                ['name' => $name],
                Response::HTTP_OK
            ),
        ]);
        self::assertSame($name, $channel->server_name);
        self::assertDatabaseHas(
            'channels',
            [
                'id' => $channel->id,
                'server_id' => $channel->server_id,
                'server_name' => $name,
                'type' => Channel::TYPE_DISCORD,
            ]
        );
    }

    /**
     * Test trying to get a server's name if the type isn't set.
     * @small
     * @test
     */
    public function testGetServerNameUnknownType(): void
    {
        $channel = new Channel();
        self::assertNull($channel->server_name);
    }

    /**
     * Test that trying to set an invalid system throws an exception.
     * @small
     * @test
     */
    public function testSetInvalidSystem(): void
    {
        $channel = new Channel();
        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('Invalid system');
        $channel->system = 'foo';
    }

    /**
     * Test setting the system to a valid value sets it.
     * @small
     * @test
     */
    public function testSetSystem(): void
    {
        $system = \key(config('app.systems'));
        $channel = new Channel();
        $channel->system = $system;
        self::assertSame($system, $channel->system);
    }

    /**
     * Test setting the type of channel to an invalid type throws an exception.
     * @small
     * @test
     */
    public function testSetTypeInvalid(): void
    {
        $channel = new Channel();
        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('Invalid channel type');
        // @phpstan-ignore-next-line
        $channel->type = 'aol';
    }

    /**
     * Test setting the type of channel to a valid type.
     * @small
     * @test
     */
    public function testSetType(): void
    {
        $channel = new Channel();
        $channel->type = Channel::TYPE_SLACK;
        self::assertSame('slack', $channel->type);
    }

    /**
     * Test scoping results to just Slack.
     * @medium
     * @test
     */
    public function testScopeSlack(): void
    {
        // Clean up from previous runs.
        Channel::where('channel_name', 'testScopeSlack')->delete();

        self::assertEmpty(
            Channel::slack()
                ->where('channel_name', 'testScopeSlack')
                ->get()
        );

        Channel::factory()->create([
            'channel_name' => 'testScopeSlack',
            'type' => Channel::TYPE_SLACK,
        ]);
        Channel::factory()->create([
            'channel_name' => 'testScopeSlack',
            'type' => Channel::TYPE_DISCORD,
        ]);
        self::assertCount(
            1,
            Channel::slack()
                ->where('channel_name', 'testScopeSlack')
                ->get()
        );
    }

    /**
     * Test scoping results to just Discord.
     * @medium
     * @test
     */
    public function testScopeDiscord(): void
    {
        // Clean up from previous runs.
        Channel::where('channel_name', 'testScopeDiscord')->delete();

        self::assertEmpty(
            Channel::discord()
                ->where('channel_name', 'testScopeDiscord')
                ->get()
        );

        Channel::factory()->create([
            'channel_name' => 'testScopeDiscord',
            'type' => Channel::TYPE_DISCORD,
        ]);
        Channel::factory()->create([
            'channel_name' => 'testScopeDiscord',
            'type' => Channel::TYPE_SLACK,
        ]);
        self::assertCount(
            1,
            Channel::discord()
                ->where('channel_name', 'testScopeDiscord')
                ->get()
        );
    }

    /**
     * Test getting the characters linked to this user and channel.
     * @medium
     * @test
     */
    public function testCharacterNone(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make();
        self::assertNull($channel->character());
    }
}
