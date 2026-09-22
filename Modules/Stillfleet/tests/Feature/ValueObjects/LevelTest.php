<?php

declare(strict_types=1);

namespace Modules\Stillfleet\Tests\Feature\ValueObjects;

use Modules\Stillfleet\ValueObjects\Level;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;

#[Group('stillfleet')]
#[Small]
final class LevelTest extends TestCase
{
    public function testToString(): void
    {
        $level = new Level(1);
        self::assertSame('1', (string)$level);
    }

    public function testUnknownAttribute(): void
    {
        $level = new Level(1);
        // @phpstan-ignore property.notFound
        self::assertNull($level->foo);
    }

    public function testBaseRate(): void
    {
        $level = new Level(1);
        self::assertSame(25, $level->base_rate);

        $level = new Level(10);
        self::assertSame(250, $level->base_rate);
    }

    public function testScoreBonus(): void
    {
        $level = new Level(4);
        self::assertSame(0, $level->score_bonus);

        $level = new Level(5);
        self::assertSame(1, $level->score_bonus);
    }

    public function testTotalPool(): void
    {
        $level = new Level(1);
        self::assertSame(0, $level->total_pool);

        $level = new Level(2);
        self::assertSame(6, $level->total_pool);

        $level = new Level(11);
        self::assertSame(60, $level->total_pool);
    }
}
