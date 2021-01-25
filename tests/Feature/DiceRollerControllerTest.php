<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Http\Responses\SlackResponse;
use Illuminate\Http\Response;

/**
 * Tests for the DiceRollerController.
 * @covers \App\Exceptions\SlackException
 * @covers \App\Http\Controllers\DiceRollerController
 * @covers \App\Http\Requests\SlackRequest
 * @group controllers
 */
final class DiceRollerControllerTest extends \Tests\TestCase
{
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
        $response = $this->post(route('roll'), [])
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
     * @test
     */
    public function testPostFullPayload(): void
    {
        $response = $this->post(
            route('roll'),
            [
                'channel_id' => 'A123',
                'team_id' => 'B234',
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
     * @test
     */
    public function testPostHelpCommandUnregisteredChannel(): void
    {
        $response = $this->post(
            route('roll'),
            [
                'channel_id' => 'A123',
                'team_id' => 'B234',
                'text' => 'help',
                'user_id' => 'C345',
            ]
        )
            ->assertOk()
            ->assertJsonFragment([
                'color' => SlackResponse::COLOR_INFO,
                'response_type' => 'ephemeral',
                'title' => 'Commands For Unregistered Channels',
            ]);
    }
}
