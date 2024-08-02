<?php

declare(strict_types=1);

namespace Modules\Avatar\Tests\Feature\Rolls;

use App\Models\Channel;
use Facades\App\Services\DiceService;
use Modules\Avatar\Rolls\Number;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

use function json_decode;

use const PHP_EOL;

#[Group('avatar')]
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
        $result = (new Number('0 pleading', 'Aang', new Channel()))->forSlack();
        $result = json_decode((string)$result)->attachments[0];
        self::assertSame('good', $result->color);
        self::assertSame('Aang hit for "pleading"!', $result->title);
        self::assertSame('Rolled 10 (4+6)', $result->text);
    }

    #[Group('discord')]
    public function testMissWithNegativeModifier(): void
    {
        DiceService::shouldReceive('rollOne')->times(2)->with(6)->andReturn(3);
        self::assertSame(
            '**Aang missed!**' . PHP_EOL . 'Rolled 3 (3+3-3)',
            (new Number('-9', 'Aang', new Channel()))->forDiscord(),
        );
    }

    #[Group('irc')]
    public function testWeakHitWithPositiveModifier(): void
    {
        DiceService::shouldReceive('rollOne')->times(2)->with(6)->andReturn(2);
        self::assertSame(
            'Aang weakly hit!' . PHP_EOL . 'Rolled 8 (2+2+4)',
            (new Number('9', 'Aang', new Channel()))->forIrc(),
        );
    }
}
