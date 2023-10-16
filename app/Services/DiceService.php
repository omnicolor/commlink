<?php

declare(strict_types=1);

namespace App\Services;

use RuntimeException;

/**
 * Service to roll one or more dice.
 *
 * This should be used as a Laravel Facade
 * (use Facades\App\Services\DiceService) so that you can test various outcomes
 * of rolling the dice.
 *
 * To roll 3d6, you'd use `DiceService::rollMany(3, 6)`.
 *
 * Testing different "random" outcomes:
 *
 * All ones:
 * `DiceService::shouldReceive('rollMany')->times(3)->with(6)->andReturn([1, 1, 1]);`
 *
 * All sixes:
 * `DiceService::shouldReceive('rollMany')->times(3)->with(6)->andReturn([6, 6, 6]);`
 * @psalm-suppress UnusedClass
 */
class DiceService
{
    /**
     * @return array<int, int>
     */
    public static function rollMany(int $dice, int $pips): array
    {
        if (1 > $dice) {
            throw new RuntimeException('You can\'t roll fewer than 1 die');
        }

        $rolls = [];
        while ($dice-- > 0) {
            $rolls[] = self::rollOne($pips);
        }
        return $rolls;
    }

    public static function rollOne(int $pips): int
    {
        if (1 >= $pips) {
            throw new RuntimeException('Dice must have more than one pip');
        }
        return random_int(1, $pips);
    }
}
