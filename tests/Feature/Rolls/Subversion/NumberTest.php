<?php

declare(strict_types=1);

namespace Tests\Feature\Rolls\Subversion;

use App\Http\Responses\Slack\SlackResponse;
use App\Models\Channel;
use App\Rolls\Subversion\Number;
use Facades\App\Services\DiceService;
use Tests\TestCase;

use const PHP_EOL;

/**
 * Tests for rolling dice in Subversion.
 * @group subversion
 * @medium
 */
final class NumberTest extends TestCase
{
    /**
     * Test trying to roll a simple three-dice roll without an attribute or TN
     * set in Slack.
     * @group slack
     */
    public function testSimpleRollSlack(): void
    {
        DiceService::shouldReceive('rollMany')
            ->once()
            ->with(3, 6)
            ->andReturn([4, 4, 4]);

        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'subversion']);
        $response = json_decode(
            (string)(new Number('3', 'username', $channel))->forSlack()
        );
        self::assertSame(
            'in_channel',
            $response->response_type
        );
        self::assertEquals(
            (object)[
                'color' => SlackResponse::COLOR_INFO,
                'text' => 'Rolled 3 dice: 4 + 4 + 4 = 12',
                'title' => 'username rolled 12',
                'footer' => '4 4 4',
            ],
            $response->attachments[0]
        );
    }

    /**
     * Test trying to roll a simple three-dice roll without the TN set in
     * Discord, but including an attribute.
     * @group discord
     */
    public function testSimpleRollDiscord(): void
    {
        DiceService::shouldReceive('rollMany')
            ->once()
            ->with(4, 6)
            ->andReturn([5, 5, 5, 5]);

        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'subversion']);
        $response = (new Number('4', 'username', $channel))->forDiscord();
        self::assertEquals(
            '**username rolled 15**' . PHP_EOL
                . 'Rolled 4 dice: 5 + 5 + 5 = 15' . PHP_EOL
                . 'Rolls: 5 5 5 5',
            $response
        );
    }

    /**
     * @group irc
     */
    public function testRollIrc(): void
    {
        DiceService::shouldReceive('rollMany')
            ->once()
            ->with(4, 6)
            ->andReturn([5, 5, 5, 5]);

        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'subversion']);
        $response = (new Number('4', 'username', $channel))->forIrc();
        self::assertEquals(
            'username rolled 15' . PHP_EOL
                . 'Rolled 4 dice: 5 + 5 + 5 = 15' . PHP_EOL
                . 'Rolls: 5 5 5 5',
            $response
        );
    }

    /**
     * @group discord
     */
    public function testDulled5Roll(): void
    {
        DiceService::shouldReceive('rollMany')
            ->once()
            ->with(3, 6)
            ->andReturn([6, 6, 6]);

        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'subversion']);
        $response = (new Number('2', 'username', $channel))->forDiscord();
        self::assertEquals(
            '**username rolled 15**' . PHP_EOL
                . 'Rolled 3 dulled (5) dice: 5 + 5 + 5 = 15' . PHP_EOL
                . 'Rolls: 6 6 6',
            $response
        );
    }

    /**
     * @group discord
     */
    public function testDulled4Roll(): void
    {
        DiceService::shouldReceive('rollMany')
            ->once()
            ->with(3, 6)
            ->andReturn([6, 6, 6]);

        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'subversion']);
        $response = (new Number('1', 'username', $channel))->forDiscord();
        self::assertEquals(
            '**username rolled 12**' . PHP_EOL
                . 'Rolled 3 dulled (4) dice: 4 + 4 + 4 = 12' . PHP_EOL
                . 'Rolls: 6 6 6',
            $response
        );
    }

    /**
     * @group discord
     */
    public function testDulled3Roll(): void
    {
        DiceService::shouldReceive('rollMany')
            ->once()
            ->with(3, 6)
            ->andReturn([6, 6, 6]);

        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'subversion']);
        $response = (new Number('0', 'username', $channel))->forDiscord();
        self::assertEquals(
            '**username rolled 9**' . PHP_EOL
                . 'Rolled 3 dulled (3) dice: 3 + 3 + 3 = 9' . PHP_EOL
                . 'Rolls: 6 6 6',
            $response
        );
    }
}
