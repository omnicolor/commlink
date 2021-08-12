<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Http\Responses\SlackResponse;
use App\Models\Channel;
use App\Models\Character;
use App\Models\ChatCharacter;
use App\Models\ChatUser;
use Str;

/**
 * Tests for the SlackController.
 * @group controllers
 * @group slack
 * @medium
 */
final class SlackControllerTest extends \Tests\TestCase
{
    use \phpmock\phpunit\PHPMock;

    /**
     * Channel used for tests.
     * @var ?Channel
     */
    protected ?Channel $channel;

    /**
     * Mock random_int function to take randomness out of testing.
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected \PHPUnit\Framework\MockObject\MockObject $randomInt;

    /**
     * Set up the mock random function each time.
     */
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
        $response = $this->options(route('roll-options'))
            ->assertOk();
        self::assertSame('OK', $response->content());
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
                'color' => SlackResponse::COLOR_INFO,
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
        // @phpstan-ignore-next-line
        $this->channel = Channel::factory()->create([
            'type' => Channel::TYPE_SLACK,
            'system' => 'shadowrun5e',
        ]);
        $this->post(
            route('roll'),
            [
                // @phpstan-ignore-next-line
                'channel_id' => $this->channel->channel_id,
                // @phpstan-ignore-next-line
                'team_id' => $this->channel->server_id,
                'text' => 'help',
                'user_id' => 'E567',
            ]
        )
            ->assertOk()
            ->assertJsonFragment([
                'color' => SlackResponse::COLOR_INFO,
                'response_type' => 'ephemeral',
                'title' => 'Commlink - Shadowrun 5E',
            ]);
    }

    /**
     * Test a Slack command for rolling dice in a Shadowrun 5E channel.
     * @test
     */
    public function testRollDiceShadowrun(): void
    {
        $this->randomInt->expects(self::any())->willReturn(5);
        // @phpstan-ignore-next-line
        $this->channel = Channel::factory()->create([
            'system' => 'shadowrun5e',
            'type' => Channel::TYPE_SLACK,
        ]);
        $this->post(
            route('roll'),
            [
                // @phpstan-ignore-next-line
                'channel_id' => $this->channel->channel_id,
                // @phpstan-ignore-next-line
                'team_id' => $this->channel->server_id,
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
     * Test a Slack command for rolling dice with a linked character.
     * @test
     */
    public function testRollDiceShadowrunWithCharacter(): void
    {
        $this->randomInt->expects(self::any())->willReturn(5);

        $slackUserId = 'E567';

        // @phpstan-ignore-next-line
        $this->channel = Channel::factory()->create([
            'system' => 'shadowrun5e',
            'type' => Channel::TYPE_SLACK,
        ]);

        $character = Character::factory()->create([
            'handle' => 'Bobby-Jo',
            'system' => 'shadowrun5e',
        ]);

        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $slackUserId,
            'server_id' => $this->channel->server_id,
            'server_type' => Channel::TYPE_SLACK,
            'verified' => true,
        ]);

        $chatCharacter = ChatCharacter::factory()->create([
            'channel_id' => $this->channel,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser,
        ]);

        $this->post(
            route('roll'),
            [
                // @phpstan-ignore-next-line
                'channel_id' => $this->channel->channel_id,
                // @phpstan-ignore-next-line
                'team_id' => $this->channel->server_id,
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
            ->assertSee('Bobby-Jo rolled 5 dice');
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
        // @phpstan-ignore-next-line
        $this->channel = Channel::factory()->create([
            'system' => 'expanse',
        ]);
        $this->post(
            route('roll'),
            [
                // @phpstan-ignore-next-line
                'channel_id' => $this->channel->channel_id,
                // @phpstan-ignore-next-line
                'team_id' => $this->channel->server_id,
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
}
