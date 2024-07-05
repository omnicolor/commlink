<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Events\RollEvent;
use App\Http\Responses\Slack\SlackResponse;
use App\Models\Campaign;
use App\Models\Channel;
use App\Models\Character;
use App\Models\ChatCharacter;
use App\Models\ChatUser;
use App\Models\User;
use Facades\App\Services\DiceService;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use const PHP_EOL;

#[Group('slack')]
#[Medium]
final class SlackControllerTest extends TestCase
{
    use WithFaker;

    /**
     * Test an OPTIONS request to the dice roller.
     */
    public function testOptions(): void
    {
        self::options(route('roll-options'))
            ->assertOk()
            ->assertSee('OK');
    }

    /**
     * Test a POST request to the dice roller without required fields.
     *
     * Slack expects 200 responses for everything.
     */
    public function testPostNoPayload(): void
    {
        self::post(route('roll'), [])
            ->assertOk()
            ->assertJsonFragment([
                'color' => 'danger',
                'response_type' => 'ephemeral',
                'text' => 'You must include at least one command argument.'
                    . PHP_EOL
                    . 'For example: `/roll init` to roll your character\'s '
                    . 'initiative.' . PHP_EOL . PHP_EOL
                    . 'Type `/roll help` for more help.',
                'title' => 'Error',
            ]);
    }

    /**
     * Test a POST request to the dice roller that has all of the required
     * fields. The values at this point don't matter, there's nothing for it to
     * do.
     */
    public function testPostFullPayload(): void
    {
        self::post(
            route('roll'),
            [
                'channel_id' => Str::random(12),
                'team_id' => Str::random(12),
                'text' => 'error',
                'user_id' => 'C345',
            ]
        )
            ->assertOk()
            ->assertJsonFragment([
                'color' => 'danger',
                'response_type' => 'ephemeral',
                'text' => 'That doesn\'t appear to be a valid Commlink command.'
                    . PHP_EOL . PHP_EOL
                    . 'Type `/roll help` for more help.',
                'title' => 'Error',
            ]);
    }

    /**
     * Test a POST request for a valid command.
     */
    public function testPostHelpCommandUnregisteredChannel(): void
    {
        self::post(
            route('roll'),
            [
                'channel_id' => 'B234',
                'team_id' => 'C345',
                'text' => 'help',
                'user_id' => 'D456',
            ]
        )
            ->assertOk()
            ->assertJsonFragment([
                'color' => SlackResponse::COLOR_DANGER,
                'response_type' => 'ephemeral',
                'title' => 'Commands for unregistered channels:',
            ]);
    }

    /**
     * Test a Slash command for getting help in a registered channel.
     */
    public function testGetHelpInRegisteredChannel(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->create([
            'type' => Channel::TYPE_SLACK,
            'system' => 'shadowrun5e',
        ]);
        self::post(
            route('roll'),
            [
                'channel_id' => $channel->channel_id,
                'team_id' => $channel->server_id,
                'text' => 'help',
                'user_id' => 'E567',
            ]
        )
            ->assertOk()
            ->assertJsonFragment([
                'color' => SlackResponse::COLOR_INFO,
                'response_type' => 'ephemeral',
                'title' => 'Commlink - Shadowrun 5th Edition',
            ]);
    }

    /**
     * Test a Slack command for rolling dice in a Shadowrun 5E channel.
     */
    public function testRollDiceShadowrun(): void
    {
        DiceService::shouldReceive('rollOne')
            ->with(6)
            ->andReturn(5);

        /** @var Channel */
        $channel = Channel::factory()->create([
            'system' => 'shadowrun5e',
            'type' => Channel::TYPE_SLACK,
        ]);
        self::post(
            route('roll'),
            [
                'channel_id' => $channel->channel_id,
                'team_id' => $channel->server_id,
                'text' => '5',
                'user_id' => 'E567',
                'user_name' => 'Bob',
            ]
        )
            ->assertOk()
            ->assertJsonFragment(['response_type' => 'in_channel'])
            ->assertSee('Rolled 5 successes')
            ->assertSee('Bob rolled 5 dice');
    }

    public function testRollModuleRoll(): void
    {
        DiceService::shouldReceive('rollOne')
            ->with(10)
            ->andReturn(4);

        /** @var Channel */
        $channel = Channel::factory()->create([
            'system' => 'transformers',
            'type' => Channel::TYPE_SLACK,
        ]);
        self::post(
            route('roll'),
            [
                'channel_id' => $channel->channel_id,
                'team_id' => $channel->server_id,
                'text' => '5',
                'user_id' => 'E567',
                'user_name' => 'Bob',
            ]
        )
            ->assertOk()
            ->assertJsonFragment(['response_type' => 'in_channel'])
            ->assertSee('4 < 5', false)
            ->assertSee('Bob rolled a success');
    }

    /**
     * Test trying a generic number command in a channel for a system that
     * doesn't have it.
     */
    public function testRollNumberUnsupported(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->create([
            'system' => 'dnd5e',
            'type' => Channel::TYPE_SLACK,
        ]);
        self::post(
            route('roll'),
            [
                'channel_id' => $channel->channel_id,
                'team_id' => $channel->server_id,
                'text' => '5',
                'user_id' => 'E567',
                'user_name' => 'Bob',
            ]
        )
            ->assertOk()
            ->assertJsonFragment(['response_type' => 'ephemeral'])
            ->assertSee(
                'That doesn\'t appear to be a valid Commlink command.',
                false
            );
    }

    /**
     * Test a Slack command for rolling dice with a linked character.
     */
    public function testRollDiceShadowrunWithCharacter(): void
    {
        DiceService::shouldReceive('rollOne')->with(6)->andReturn(5);

        $slackUserId = 'E567';

        /** @var Channel */
        $channel = Channel::factory()->create([
            'system' => 'shadowrun5e',
            'type' => Channel::TYPE_SLACK,
        ]);

        /** @var Character */
        $character = Character::factory()->create([
            'system' => 'shadowrun5e',
            'created_by' => self::class . '::' . __FUNCTION__,
        ]);

        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $slackUserId,
            'server_id' => $channel->server_id,
            'server_type' => Channel::TYPE_SLACK,
            'verified' => true,
        ]);

        $chatCharacter = ChatCharacter::factory()->create([
            'channel_id' => $channel,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser,
        ]);

        self::post(
            route('roll'),
            [
                'channel_id' => $channel->channel_id,
                'team_id' => $channel->server_id,
                'text' => '5',
                'user_id' => $slackUserId,
                'user_name' => 'Bob',
            ]
        )
            ->assertOk()
            ->assertJsonFragment(['response_type' => 'in_channel'])
            ->assertSee('Rolled 5 successes')
            ->assertDontSee('Bob rolled 5 dice')
            ->assertSee(sprintf('%s rolled 5 dice', (string)$character), false);
        $character->delete();
    }

    /**
     * Test a Slack command for rolling a system-specific, non-numeric roll.
     */
    public function testRollSystemSpecificNonNumeric(): void
    {
        Event::fake();

        $slackUserId = 'U' . Str::random(8);

        /** @var Campaign */
        $campaign = Campaign::factory()->create([
            'options' => ['nightCityTarot' => true],
            'system' => 'cyberpunkred',
        ]);

        /** @var Channel */
        $channel = Channel::factory()->create([
            'campaign_id' => $campaign,
            'system' => 'cyberpunkred',
            'type' => Channel::TYPE_SLACK,
        ]);

        /** @var Character */
        $character = Character::factory()->create([
            'system' => 'cyberpunkred',
            'created_by' => self::class . '::' . __FUNCTION__,
        ]);

        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $slackUserId,
            'server_id' => $channel->server_id,
            'server_type' => Channel::TYPE_SLACK,
            'verified' => true,
        ]);

        self::post(
            route('roll'),
            [
                'channel_id' => $channel->channel_id,
                'team_id' => $channel->server_id,
                'text' => 'tarot',
                'user_id' => $slackUserId,
                'user_name' => 'Bob',
            ]
        )
            ->assertOk()
            ->assertJsonFragment(['response_type' => 'in_channel'])
            ->assertSee('Bob drew')
            ->assertSee('21 cards remain');

        Event::assertDispatched(RollEvent::class);
        $character->delete();
    }

    /**
     * Test trying to `/roll 5` in an unregistered channel.
     */
    public function testRollDiceUnregistered(): void
    {
        self::post(
            route('roll'),
            [
                'channel_id' => Str::random(11),
                'team_id' => Str::random(12),
                'text' => '5',
                'user_id' => Str::random(9),
            ]
        )
            ->assertOk()
            ->assertJsonFragment([
                'color' => SlackResponse::COLOR_DANGER,
                'response_type' => 'ephemeral',
                'text' => 'That doesn\'t appear to be a valid Commlink command.'
                    . PHP_EOL . PHP_EOL
                    . 'Type `/roll help` for more help.',
                'title' => 'Error',
            ]);
    }

    /**
     * Test trying to `/roll 1d20' in an unregistered channel.
     */
    public function testRollGenericDiceUnregistered(): void
    {
        DiceService::shouldReceive('rollMany')->with(1, 20)->andReturn([5]);
        self::post(
            route('roll'),
            [
                'channel_id' => Str::random(12),
                'team_id' => Str::random(12),
                'text' => '1d20',
                'user_id' => 'E567',
                'username' => 'Bob',
            ]
        )
            ->assertOk()
            ->assertJsonFragment(['response_type' => 'in_channel'])
            ->assertSee('Rolling: 1d20 = [5] = 5')
            ->assertDontSee('Rolls: 5');
    }

    /**
     * Test trying to `/roll 5` in an channel registered to a system that
     * doesn't use that format.
     */
    public function testRollDiceInvalidNumericForSystem(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->create(['system' => 'dnd5e']);
        self::post(
            route('roll'),
            [
                'channel_id' => $channel->channel_id,
                'team_id' => $channel->server_id,
                'text' => '5',
                'user_id' => Str::random(9),
            ]
        )
            ->assertOk()
            ->assertJsonFragment([
                'color' => SlackResponse::COLOR_DANGER,
                'response_type' => 'ephemeral',
                'text' => 'That doesn\'t appear to be a valid Commlink command.'
                    . PHP_EOL . PHP_EOL
                    . 'Type `/roll help` for more help.',
                'title' => 'Error',
            ]);
    }

    /**
     * Test trying to `/roll info`.
     */
    public function testRollInfo(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->create([]);
        self::post(
            route('roll'),
            [
                'channel_id' => $channel->channel_id,
                'team_id' => $channel->server_id,
                'text' => 'info',
                'user_id' => Str::random(9),
            ]
        )
            ->assertOk()
            ->assertJsonFragment(['value' => 'No campaign']);
    }

    /**
     * Test a non-generic, non-system roll.
     */
    public function testFlipCoin(): void
    {
        Event::fake();
        DiceService::shouldReceive('rollOne')
            ->once()
            ->with(2)
            ->andReturn(1);

        self::post(
            route('roll'),
            [
                'channel_id' => Str::random(12),
                'team_id' => Str::random(12),
                'text' => 'coin',
                'user_id' => 'E567',
                'username' => 'Bob',
            ]
        )
            ->assertOk()
            ->assertJsonFragment(['response_type' => 'in_channel'])
            ->assertSee('flipped a coin: ');

        Event::assertDispatched(RollEvent::class);
    }

    public function testAuthThroughSlack(): void
    {
        self::get('/slack/auth')
            ->assertRedirectContains('https://slack.com/oauth/v2/authorize?');
    }

    public function testLoginThroughSlackExistingUserNewChatUser(): void
    {
        /** @var User */
        $user = User::factory()->create();

        $slackUserId = 'U' . Str::random(8);
        $slackUserName = $this->faker->name();
        $teamId = Str::random(12);
        $teamName = $this->faker->word();
        Socialite::shouldReceive('driver->user')
            ->andReturn((object)[
                'email' => $user->email,
                'id' => $slackUserId,
                'name' => $slackUserName,
                'attributes' => [
                    'organization_id' => $teamId,
                ],
                'user' => [
                    'team' => [
                        'name' => $teamName,
                    ],
                ],
            ]);

        self::assertDatabaseMissing(
            'chat_users',
            [
                'server_id' => $teamId,
                'server_name' => $teamName,
                'server_type' => ChatUser::TYPE_SLACK,
                'remote_user_id' => $slackUserId,
                'remote_user_name' => $slackUserName,
                'user_id' => $user->id,
            ]
        );
        self::get('slack/callback')->assertRedirect('/dashboard');
        self::assertAuthenticatedAs($user);
        self::assertDatabaseHas(
            'chat_users',
            [
                'server_id' => $teamId,
                'server_name' => $teamName,
                'server_type' => ChatUser::TYPE_SLACK,
                'remote_user_id' => $slackUserId,
                'remote_user_name' => $slackUserName,
                'user_id' => $user->id,
            ]
        );
    }

    /**
     * Test that logging in through Slack as a new user creates a User and
     * a ChatUser.
     */
    public function testLoginThroughSlackNewUser(): void
    {
        // Find an email that hasn't been used yet.
        do {
            $email = $this->faker->email();
        } while (null !== User::where('email', $email)->first());

        $slackUserId = 'U' . Str::random(8);
        $slackUserName = $this->faker->name();
        $teamId = Str::random(12);
        $teamName = $this->faker->word();
        Socialite::shouldReceive('driver->user')
            ->andReturn((object)[
                'email' => $email,
                'id' => $slackUserId,
                'name' => $slackUserName,
                'attributes' => [
                    'organization_id' => $teamId,
                ],
                'user' => [
                    'team' => [
                        'name' => $teamName,
                    ],
                ],
            ]);
        self::get('/slack/callback')->assertRedirect('/dashboard');

        /** @var User */
        $user = User::where('email', $email)->first();
        self::assertSame($slackUserName, $user->name);
        self::assertSame('reset me', $user->password);
        self::assertAuthenticatedAs($user);
        self::assertDatabaseHas(
            'chat_users',
            [
                'server_id' => $teamId,
                'server_name' => $teamName,
                'server_type' => ChatUser::TYPE_SLACK,
                'remote_user_id' => $slackUserId,
                'remote_user_name' => $slackUserName,
                'user_id' => $user->id,
            ]
        );
    }

    /**
     * Test a Slack-specific response not tied to a system.
     */
    public function testSlackResponseNotForSystem(): void
    {
        /** @var User */
        $user = User::factory()->create();

        $slackUserId = 'U' . Str::random(8);

        /** @var Channel */
        $channel = Channel::factory()->create([
            'system' => 'cyberpunkred',
            'server_id' => 'T' . Str::random(10),
            'type' => Channel::TYPE_SLACK,
        ]);

        /** @var Character */
        $character = Character::factory()->create([
            'owner' => $user->email,
            'system' => 'cyberpunkred',
            'created_by' => self::class . '::' . __FUNCTION__,
        ]);

        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $slackUserId,
            'server_id' => $channel->server_id,
            'server_type' => Channel::TYPE_SLACK,
            'user_id' => $user,
            'verified' => true,
        ]);

        $this->post(
            route('roll'),
            [
                'channel_id' => $channel->channel_id,
                'team_id' => $channel->server_id,
                'text' => 'link ' . $character->id,
                'user_id' => $slackUserId,
                'username' => 'Bob',
            ]
        )
            ->assertOk()
            ->assertSee('You have linked');
    }

    public function testHandleActionWithInvalidPayload(): void
    {
        self::withHeaders(['Accept' => 'application/json'])
            ->post(route('roll'), ['payload' => 'test'])
            ->assertOk()
            ->assertJsonFragment(['text' => 'Invalid action payload']);
    }

    public function testHandleActionWithInvalidAction(): void
    {
        self::withHeaders(['Accept' => 'application/json'])
            ->post(
                route('roll'),
                [
                    'payload' => json_encode([
                        'team' => ['id' => 'T' . Str::random(6)],
                        'channel' => ['id' => 'C' . Str::random(6)],
                        'user' => [
                            'id' => 'U' . Str::random(6),
                            'name' => 'Bob',
                        ],
                        'actions' => [
                            [
                                'action_id' => 'foo',
                            ],
                        ],
                    ]),
                ],
            )
            ->assertOk()
            ->assertJsonFragment(['text' => 'Invalid action callback']);
    }

    public function testHandleActionWithValidAction(): void
    {
        self::withHeaders(['Accept' => 'application/json'])
            ->post(
                route('roll'),
                [
                    'payload' => json_encode([
                        'team' => ['id' => 'T' . Str::random(6)],
                        'channel' => ['id' => 'C' . Str::random(6)],
                        'user' => [
                            'id' => 'U' . Str::random(6),
                            'name' => 'Bob',
                        ],
                        'actions' => [
                            [
                                'action_id' => 'rsvp:13',
                            ],
                        ],
                    ]),
                ],
            )
            ->assertOk();
    }
}
