<?php

declare(strict_types=1);

namespace Tests\Feature\Rolls\Expanse;

use App\Models\Channel;
use App\Rolls\Expanse\Number;
use Facades\App\Services\DiceService;
use Tests\TestCase;

use function explode;

use const PHP_EOL;

/**
 * Tests for rolling dice in The Expanse.
 * @group expanse
 * @medium
 */
final class NumberTest extends TestCase
{
    /**
     * Test a basic roll generating stunt points in Slack without a description.
     * @group slack
     */
    public function testSimpleRollSlack(): void
    {
        DiceService::shouldReceive('rollMany')
            ->once()
            ->with(3, 6)
            ->andReturn([3, 3, 3]);

        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'expanse']);
        $response = \json_decode(
            (string)(new Number('5', 'user', $channel))->forSlack()
        );
        self::assertCount(1, $response->attachments);
        self::assertSame('user made a roll', $response->attachments[0]->title);
        self::assertSame('14 (3 SP)', $response->attachments[0]->text);
        self::assertSame('3 3 `3`', $response->attachments[0]->footer);
    }

    /**
     * Test a basic roll generating stunt points in Discord without a
     * description.
     * @group discord
     */
    public function testSimpleRollDiscord(): void
    {
        DiceService::shouldReceive('rollMany')
            ->once()
            ->with(3, 6)
            ->andReturn([3, 3, 3]);

        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'expanse']);
        $response = (new Number('5', 'user', $channel))->forDiscord();
        $response = explode(PHP_EOL, $response);
        self::assertSame('**user made a roll**', $response[0]);
        self::assertSame('14 (3 SP)', $response[1]);
    }

    /**
     * Test a basic roll not generating stunt points in Discord with a
     * description.
     * @group discord
     */
    public function testRollWithDescriptionDiscord(): void
    {
        DiceService::shouldReceive('rollMany')
            ->once()
            ->with(3, 6)
            ->andReturn([2, 3, 5]);

        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'expanse']);
        $response = (new Number('5 percept', 'user', $channel))->forDiscord();
        $response = explode(PHP_EOL, $response);
        self::assertSame('**user made a roll for "percept"**', $response[0]);
        self::assertSame('15', $response[1]);
    }

    /**
     * Test a basic roll not generating stunt points in IRC with a description.
     * @group irc
     */
    public function testRollWithDescriptionIrc(): void
    {
        DiceService::shouldReceive('rollMany')
            ->once()
            ->with(3, 6)
            ->andReturn([2, 3, 5]);

        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'expanse']);

        $response = (new Number('5 percept', 'user', $channel))->forIrc();
        $response = explode(PHP_EOL, $response);
        self::assertSame('user made a roll for "percept"', $response[0]);
        self::assertSame('15', $response[1]);
    }
}
