<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Http\Responses\SlackResponse;
use App\Models\Channel;
use Str;

/**
 * Tests for the SlackController.
 * @covers \App\Exceptions\SlackException
 * @covers \App\Http\Controllers\SlackController
 * @covers \App\Http\Requests\SlackRequest
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
     * Clean up after the tests.
     */
    public function tearDown(): void
    {
        if (isset($this->channel)) {
            $this->channel->delete();
            $this->channel = null;
        }
        parent::tearDown();
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
        $this->channel = Channel::factory()->create([
            'system' => 'shadowrun5e',
        ]);
        $this->post(
            route('roll'),
            [
                'channel_id' => $this->channel->channel_id,
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
        $this->channel = Channel::factory()->create([
            'system' => 'shadowrun5e',
        ]);
        $this->post(
            route('roll'),
            [
                'channel_id' => $this->channel->channel_id,
                'team_id' => $this->channel->server_id,
                'text' => '5',
                'user_id' => 'E567',
            ]
        )
            ->assertOk()
            ->assertJsonFragment([
                'response_type' => 'in_channel',
            ])
            ->assertSee('Rolled 5 successes');
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
        $this->channel = Channel::factory()->create([
            'system' => 'expanse',
        ]);
        $this->post(
            route('roll'),
            [
                'channel_id' => $this->channel->channel_id,
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
