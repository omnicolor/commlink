<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Enums\ChannelType;
use App\Models\Campaign;
use App\Models\Channel;
use App\Models\Character;
use App\Models\ChatCharacter;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use RuntimeException;
use Tests\TestCase;

use function array_keys;
use function key;
use function sprintf;

#[Group('discord')]
#[Group('slack')]
#[Medium]
final class ChannelTest extends TestCase
{
    use WithFaker;

    /**
     * URL that Slack connections will go to.
     * @var string
     */
    protected const string API_SLACK_TEAMS = 'slack.com/api/auth.teams.list';

    /**
     * URL that Discord connections will go to.
     * @var string
     */
    protected const string API_DISCORD_GUILDS = 'https://discord.com/api/guilds/';

    /**
     * Test getting the server's name if we've already retrieved it.
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
     */
    public function testGetServerNameNewSlackInstance(): void
    {
        $channel = new Channel([
            'server_id' => 'T' . Str::random(10),
            'type' => ChannelType::Slack,
        ]);
        self::assertDatabaseMissing(
            'channels',
            [
                'server_id' => $channel->server_id,
                'type' => ChannelType::Slack,
            ]
        );
        $name = Str::random(20);
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
                'type' => ChannelType::Slack,
            ]
        );
    }

    /**
     * Test that getting a server's name for the first time on a saved instance
     * updates the database.
     */
    public function testGetServerNameSlackInstance(): void
    {
        $user = User::factory()->create();
        $channel = new Channel([
            'channel_id' => 'C' . Str::random(10),
            'registered_by' => $user->id,
            'server_id' => 'T' . Str::random(10),
            'system' => $this->faker->randomElement(array_keys(config('commlink.systems'))),
            'type' => ChannelType::Slack,
        ]);
        $channel->save();
        $name = Str::random(20);
        self::assertDatabaseHas(
            'channels',
            [
                'id' => $channel->id,
                'server_id' => $channel->server_id,
                'server_name' => null,
                'type' => ChannelType::Slack,
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
                'type' => ChannelType::Slack,
            ]
        );
    }

    /**
     * Test getting the server's name for a Discord instance that hasn't been
     * saved.
     */
    public function testGetServerNameNewDiscordInstance(): void
    {
        $channel = new Channel([
            'server_id' => '1' . Str::random(10),
            'type' => ChannelType::Discord,
        ]);
        self::assertDatabaseMissing(
            'channels',
            [
                'server_id' => $channel->server_id,
                'type' => ChannelType::Discord,
            ]
        );
        $name = Str::random(20);
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
                'type' => ChannelType::Discord,
            ]
        );
    }

    /**
     * Test that getting a server's name for the first time on a saved instance
     * updates the database.
     */
    public function testGetServerNameDiscordInstance(): void
    {
        Http::preventStrayRequests();

        $user = User::factory()->create();
        $channel = new Channel([
            'channel_id' => '2' . Str::random(10),
            'registered_by' => $user->id,
            'server_id' => '1' . Str::random(10),
            'system' => $this->faker->randomElement(array_keys(config('commlink.systems'))),
            'type' => ChannelType::Discord,
        ]);
        $channel->save();
        $name = Str::random(20);
        self::assertDatabaseHas(
            'channels',
            [
                'id' => $channel->id,
                'server_id' => $channel->server_id,
                'server_name' => null,
                'type' => ChannelType::Discord,
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
                'type' => ChannelType::Discord,
            ]
        );
    }

    /**
     * Test trying to get a server's name if the type isn't set.
     */
    public function testGetServerNameUnknownType(): void
    {
        $channel = new Channel();
        self::assertNull($channel->server_name);
    }

    /**
     * Test that trying to set an invalid system throws an exception.
     */
    public function testSetInvalidSystem(): void
    {
        $channel = new Channel();
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Invalid system');
        $channel->system = 'foo';
    }

    /**
     * Test setting the system to a valid value sets it.
     */
    public function testSetSystem(): void
    {
        $system = key(config('commlink.systems'));
        $channel = new Channel();
        $channel->system = $system;
        self::assertSame($system, $channel->system);
    }

    /**
     * Test setting the type of channel to a valid type.
     */
    public function testSetType(): void
    {
        $channel = new Channel();
        $channel->type = ChannelType::Slack;
        self::assertSame(ChannelType::Slack, $channel->type);
    }

    /**
     * Test scoping results to just Slack.
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
            'type' => ChannelType::Slack,
        ]);
        Channel::factory()->create([
            'channel_name' => 'testScopeSlack',
            'type' => ChannelType::Discord,
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
            'type' => ChannelType::Discord,
        ]);
        Channel::factory()->create([
            'channel_name' => 'testScopeDiscord',
            'type' => ChannelType::Slack,
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
     */
    public function testCharacterNone(): void
    {
        $channel = Channel::factory()->make();
        self::assertNull($channel->character());
    }

    /**
     * Test getting the campaign attached to the channel when there isn't one.
     */
    public function testCampaignNone(): void
    {
        $channel = Channel::factory()->make();
        self::assertEmpty($channel->campaign);
    }

    /**
     * Test getting the campaign attached to the channel.
     */
    public function testCampaign(): void
    {
        $campaign = Campaign::factory()->create();
        $channel = Channel::factory()->make(['campaign_id' => $campaign]);
        self::assertSame($campaign->id, $channel->campaign?->id);
    }

    public function testCharactersWithoutAny(): void
    {
        $channel = Channel::factory()->make();
        self::assertEmpty($channel->characters());
    }

    public function testCharacters(): void
    {
        $channel = Channel::factory()->create();

        // Add a character to the channel.
        $character = Character::factory()->create();
        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->_id,
        ]);

        // And an invalid character.
        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => 'not-found',
        ]);

        self::assertCount(1, $channel->characters());
        $character->delete();
    }

    public function testFindForWebhookNotFound(): void
    {
        self::assertNull(Channel::findForWebhook('123', '321'));
    }

    public function testFindForWebhook(): void
    {
        $guild_id = Str::random(10);
        $webhook_id = Str::random(10);
        Channel::factory()->create([
            'server_id' => $guild_id,
            'type' => ChannelType::Discord,
            'webhook' => sprintf(
                'https://discord.com/api/webhooks/%s/%s',
                $webhook_id,
                Str::random(10),
            ),
        ]);

        self::assertNotNull(Channel::findForWebhook($guild_id, $webhook_id));
    }
}
