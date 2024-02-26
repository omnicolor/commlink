<?php

declare(strict_types=1);

namespace Tests\Feature\Rolls;

use App\Models\Channel;
use App\Rolls\Coin;
use Facades\App\Services\DiceService;
use Tests\TestCase;

use function json_decode;

use const PHP_EOL;

/**
 * Tests for flipping a coin.
 * @medium
 */
final class CoinTest extends TestCase
{
    /**
     * Test a coin flip.
     * @group slack
     * @test
     */
    public function testTails(): void
    {
        DiceService::shouldReceive('rollOne')->once()->with(2)->andReturn(2);

        /** @var Channel */
        $channel = Channel::factory()->make();
        $response = new Coin('coin', 'username', $channel);
        $response = json_decode((string)$response->forSlack());
        self::assertSame(
            'username flipped a coin: Tails',
            $response->attachments[0]->title
        );
    }

    /**
     * Test a coin flip.
     * @group discord
     * @test
     */
    public function testHeads(): void
    {
        DiceService::shouldReceive('rollOne')->once()->with(2)->andReturn(1);

        /** @var Channel */
        $channel = Channel::factory()->make();
        $response = new Coin('coin', 'username', $channel);
        $response = $response->forDiscord();
        self::assertSame(
            '**username flipped a coin: Heads**' . PHP_EOL,
            $response
        );
    }

    public function testIrc(): void
    {
        DiceService::shouldReceive('rollOne')->once()->with(2)->andReturn(1);

        /** @var Channel */
        $channel = Channel::factory()->make();
        self::assertSame(
            'username flipped a coin: Heads',
            (new Coin('coin', 'username', $channel))->forIrc(),
        );
    }
}
