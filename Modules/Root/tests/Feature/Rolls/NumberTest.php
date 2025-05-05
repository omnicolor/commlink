<?php

declare(strict_types=1);

namespace Modules\Root\Tests\Feature\Rolls;

use App\Models\Channel;
use Facades\App\Services\DiceService;
use Modules\Root\Rolls\Number;
use Omnicolor\Slack\Attachment;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

use const PHP_EOL;

#[Group('root')]
#[Small]
final class NumberTest extends TestCase
{
    #[Group('slack')]
    public function testHitWithDescription(): void
    {
        DiceService::shouldReceive('rollOne')
            ->times(2)
            ->with(6)
            ->andReturn(4, 6);
        $result = (new Number('0 pleading', 'Rat', new Channel()))
            ->forSlack()
            ->jsonSerialize();

        self::assertArrayHasKey('attachments', $result);
        self::assertSame(
            [
                'color' => Attachment::COLOR_SUCCESS,
                'text' => 'Rolled 10 (4+6)',
                'title' => 'Rat got a full hit for "pleading"!',
            ],
            $result['attachments'][0],
        );
    }

    #[Group('discord')]
    public function testMissWithNegativeModifier(): void
    {
        DiceService::shouldReceive('rollOne')->times(2)->with(6)->andReturn(3);
        self::assertSame(
            '**Aang missed!**' . PHP_EOL . 'Rolled 3 (3+3-3)',
            (new Number('-3', 'Aang', new Channel()))->forDiscord(),
        );
    }

    #[Group('irc')]
    public function testWeakHitWithPositiveModifier(): void
    {
        DiceService::shouldReceive('rollOne')->times(2)->with(6)->andReturn(3);
        self::assertSame(
            'Fox got a partial hit!' . PHP_EOL . 'Rolled 8 (3+3+2)',
            (new Number('2', 'Fox', new Channel()))->forIrc(),
        );
    }
}
