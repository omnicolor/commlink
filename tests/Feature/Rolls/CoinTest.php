<?php

declare(strict_types=1);

namespace Tests\Feature\Rolls;

use App\Models\Channel;
use App\Rolls\Coin;
use Facades\App\Services\DiceService;
use Omnicolor\Slack\Sections\Text;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use const PHP_EOL;

#[Medium]
final class CoinTest extends TestCase
{
    #[Group('slack')]
    public function testTails(): void
    {
        DiceService::shouldReceive('rollOne')->once()->with(2)->andReturn(2);

        $channel = Channel::factory()->make();
        $response = new Coin('coin', 'username', $channel);
        $response = $response->forSlack()->jsonSerialize();
        self::assertEquals(
            (new Text('username flipped a coin: Tails'))->jsonSerialize(),
            $response['blocks'][0],
        );
    }

    #[Group('discord')]
    public function testHeads(): void
    {
        DiceService::shouldReceive('rollOne')->once()->with(2)->andReturn(1);

        $channel = Channel::factory()->make();
        $response = new Coin('coin', 'username', $channel);
        $response = $response->forDiscord();
        self::assertSame(
            '**username flipped a coin: Heads**' . PHP_EOL,
            $response
        );
    }

    #[Group('irc')]
    public function testIrc(): void
    {
        DiceService::shouldReceive('rollOne')->once()->with(2)->andReturn(1);

        $channel = Channel::factory()->make();
        self::assertSame(
            'username flipped a coin: Heads',
            (new Coin('coin', 'username', $channel))->forIrc(),
        );
    }
}
