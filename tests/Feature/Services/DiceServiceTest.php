<?php

declare(strict_types=1);

namespace Tests\Feature\Services;

use App\Services\DiceService;
use RuntimeException;
use Tests\TestCase;

/**
 * @small
 */
final class DiceServiceTest extends TestCase
{
    public function testRollManyFewerThanOneDie(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('You can\'t roll fewer than 1 die');
        DiceService::rollMany(0, 0);
    }

    public function testRollManyFewerThanOnePip(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Dice must have more than one pip');
        DiceService::rollMany(1, 1);
    }

    public function testRollMany(): void
    {
        $rolls = DiceService::rollMany(10, 6);
        self::assertCount(10, $rolls);
        foreach ($rolls as $roll) {
            self::assertGreaterThanOrEqual(1, $roll);
            self::assertLessThanOrEqual(6, $roll);
        }
    }

    public function testRollOneOnePip(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Dice must have more than one pip');
        DiceService::rollOne(1);
    }

    public function testRollOne(): void
    {
        $roll = DiceService::rollOne(6);
        self::assertGreaterThanOrEqual(1, $roll);
        self::assertLessThanOrEqual(6, $roll);
    }
}
