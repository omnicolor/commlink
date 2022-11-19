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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\MockObject\MockObject;
use Str;
use Tests\TestCase;

/**
 * Tests for the SlackController.
 * @group controllers
 * @group slack
 * @medium
 */
final class SlackControllerTest extends TestCase
{
    use PHPMock;
    use RefreshDatabase;

    protected MockObject $randomInt;

    public function setUp(): void
    {
        parent::setUp();
        $this->randomInt = $this->getFunctionMock(
            'App\\Rolls\\Shadowrun5e',
            'random_int'
        );
    }

    /**
     * Test an OPTIONS request to the dice roller.
     * @test
     */
    public function testOption(): void
    {
        $this->options(route('roll-options'))
            ->assertOk()
            ->assertSee('OK');
    }

    /**
     * Test a POST request to the dice roller without required fields.
     *
     * Slack expects 200 responses for everything.
     * @test
     */
    public function testPostNoPayload(): void
    {
        $this->post(route('roll'), [])
            ->assertOk()
            ->assertJsonFragment([
                'color' => 'danger',
                'response_type' => 'ephemeral',
                'text' => 'You must include at least one command argument.'
                    . \PHP_EOL
                    . 'For example: `/roll init` to roll your character\'s '
                    . 'initiative.' . \PHP_EOL . \PHP_EOL
                    . 'Type `/roll help` for more help.',
                'title' => 'Error',
            ]);
    }

    /**
     * Test a POST request to the dice roller that has all of the required
     * fields. The values at this point don't matter, there's nothing for it to
     * do.
     * @test
     */
    public function testPostFullPayload(): void
    {
        $response = $this->post(
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
                    . \PHP_EOL . \PHP_EOL
                    . 'Type `/roll help` for more help.',
                'title' => 'Error',
            ]);
    }

    /**
     * Test a POST request for a valid command.
     * @test
     */
    public function testPostHelpCommandUnregisteredChannel(): void
    {
        $this->post(
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
     * @test
     */
    public function testGetHelpInRegisteredChannel(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->create([
            'type' => Channel::TYPE_SLACK,
            'system' => 'shadowrun5e',
        ]);
        $this->post(
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
     * @test
     */
    public function testRollDiceShadowrun(): void
    {
        $this->randomInt->expects(self::any())->willReturn(5);
        /** @var Channel */
        $channel = Channel::factory()->create([
            'system' => 'shadowrun5e',
            'type' => Channel::TYPE_SLACK,
        ]);
        $this->post(
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
            ->assertJsonFragment([
                'response_type' => 'in_channel',
            ])
            ->assertSee('Rolled 5 successes')
            ->assertSee('Bob rolled 5 dice');
    }

    /**
     * Test trying a generic number command in a channel for a system that
     * doesn't have it.
     * @test
     */
    public function testRollNumberUnsupported(): void
    {
        $this->randomInt->expects(self::any())->willReturn(5);
        /** @var Channel */
        $channel = Channel::factory()->create([
            'system' => 'dnd5e',
            'type' => Channel::TYPE_SLACK,
        ]);
        $this->post(
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
     * @test
     */
    public function testRollDiceShadowrunWithCharacter(): void
    {
        $this->randomInt->expects(self::any())->willReturn(5);

        $slackUserId = 'E567';

        /** @var Channel */
        $channel = Channel::factory()->create([
            'system' => 'shadowrun5e',
            'type' => Channel::TYPE_SLACK,
        ]);

        /** @var Character */
        $character = Character::factory()->create([
            'system' => 'shadowrun5e',
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

        $this->post(
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
            ->assertJsonFragment([
                'response_type' => 'in_channel',
            ])
            ->assertSee('Rolled 5 successes')
            ->assertDontSee('Bob rolled 5 dice')
            ->assertSee(sprintf('%s rolled 5 dice', (string)$character), false);
    }

    /**
     * Test a Slack command for rolling a system-specific, non-numeric roll.
     * @test
     */
    public function testRollSystemSpecificNonNumeric(): void
    {
        Event::fake();

        $slackUserId = 'U' . \Str::random(8);

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
        ]);

        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $slackUserId,
            'server_id' => $channel->server_id,
            'server_type' => Channel::TYPE_SLACK,
            'verified' => true,
        ]);

        $this->post(
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
            ->assertJsonFragment([
                'response_type' => 'in_channel',
            ])
            ->assertSee('Bob drew')
            ->assertSee('21 cards remain');

        Event::assertDispatched(RollEvent::class);
    }

    /**
     * Test trying to `/roll 5` in an unregistered channel.
     * @test
     */
    public function testRollDiceUnregistered(): void
    {
        $this->post(
            route('roll'),
            [
                'channel_id' => \Str::random(11),
                'team_id' => \Str::random(12),
                'text' => '5',
                'user_id' => \Str::random(9),
            ]
        )
            ->assertOk()
            ->assertJsonFragment([
                'color' => SlackResponse::COLOR_DANGER,
                'response_type' => 'ephemeral',
                'text' => 'That doesn\'t appear to be a valid Commlink command.'
                    . \PHP_EOL . \PHP_EOL
                    . 'Type `/roll help` for more help.',
                'title' => 'Error',
            ]);
    }

    /**
     * Test trying to `/roll 1d20' in an unregistered channel.
     * @test
     */
    public function testRollGenericDiceUnregistered(): void
    {
        $this->randomInt = $this->getFunctionMock(
            'App\\Rolls',
            'random_int'
        );
        $this->randomInt->expects(self::exactly(1))->willReturn(5);
        $this->post(
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
            ->assertJsonFragment([
                'response_type' => 'in_channel',
            ])
            ->assertSee('Rolling: 1d20 = [5] = 5')
            ->assertDontSee('Rolls: 5');
    }

    /**
     * Test trying to `/roll 5` in an channel registered to a system that
     * doesn't use that format.
     * @test
     */
    public function testRollDiceInvalidNumericForSystem(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->create([
            'system' => 'dnd5e',
        ]);
        $this->post(
            route('roll'),
            [
                'channel_id' => $channel->channel_id,
                'team_id' => $channel->server_id,
                'text' => '5',
                'user_id' => \Str::random(9),
            ]
        )
            ->assertOk()
            ->assertJsonFragment([
                'color' => SlackResponse::COLOR_DANGER,
                'response_type' => 'ephemeral',
                'text' => 'That doesn\'t appear to be a valid Commlink command.'
                    . \PHP_EOL . \PHP_EOL
                    . 'Type `/roll help` for more help.',
                'title' => 'Error',
            ]);
    }

    /**
     * Test trying to `/roll info`.
     * @test
     */
    public function testRollInfo(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->create([]);
        $this->post(
            route('roll'),
            [
                'channel_id' => $channel->channel_id,
                'team_id' => $channel->server_id,
                'text' => 'info',
                'user_id' => \Str::random(9),
            ]
        )
            ->assertOk()
            ->assertJsonFragment([
                'value' => 'No campaign',
            ]);
    }

    /**
     * Test a non-generic, non-system roll.
     * @test
     */
    public function testFlipCoin(): void
    {
        Event::fake();

        $this->post(
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
            ->assertJsonFragment([
                'response_type' => 'in_channel',
            ])
            ->assertSee('flipped a coin: ');

        Event::assertDispatched(RollEvent::class);
    }
}
