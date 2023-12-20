<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Shadowrun5e\ForceTrait;
use RuntimeException;

use function explode;
use function preg_match_all;
use function sprintf;

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
    use ForceTrait;

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

    /**
     * Pull the dynamic part(s) of the text out.
     *
     * For an expression like '10+9d6+27', would pull out and return '9d6'.
     * @return array<int, string>
     */
    public static function getDynamicParts(string $string): array
    {
        $matches = [];
        preg_match_all('/((\d+)?[dD]\d+)/', $string, $matches);
        return $matches[0];
    }

    /**
     * Given the dynamic part of a dice notation string, return the dice and
     * pips parts separated.
     * @return array{0: non-negative-int, 1: int<2, max>}
     */
    public static function getDiceAndPips(string $string): array
    {
        [$dice, $pips] = explode('d', strtolower($string));
        if ('' === $dice) {
            $dice = '1';
        }

        if (0 >= (int)$dice) {
            throw new RuntimeException('You can\'t roll fewer than 1 die');
        }

        if (1 >= (int)$pips) {
            throw new RuntimeException('Dice must have more than one pip');
        }

        return [(int)$dice, (int)$pips];
    }

    /**
     * Given a string of dice notation, rolls dice for all of the dynamic parts.
     * @return array<string, array<int, int>>
     */
    public static function rollDynamic(string $string): array
    {
        $parts = self::getDynamicParts($string);
        $rolls = [];
        foreach ($parts as $part) {
            [$dice, $pips] = self::getDiceAndPips($part);
            $rolls[sprintf('%dd%d', $dice, $pips)] = self::rollMany($dice, $pips);
        }
        return $rolls;
    }

    /**
     * Given a string of dice notation and math, roll dice and return
     * everything about the roll.
     * @psalm-suppress PossiblyUnusedMethod
     * @return object{total: int, rolls: array<int, int>, work: string}
     */
    public static function rollDice(string $string): object
    {
        $total = $work = $string;
        $rolls = [];
        foreach (self::rollDynamic($string) as $dynamic => $result) {
            $rolls = array_merge($rolls, $result);
            // Replace each individual XdY with the individual rolls to show
            // all math.
            $work = str_replace(
                search: $dynamic,
                replace: sprintf('(%s)', implode('+', $result)),
                subject: $work,
            );
            // Replace each individual XdY with the total for the die type to
            // calculate the total.
            $total = str_replace(
                search: $dynamic,
                replace: (string)array_sum($result),
                subject: $total,
            );
        }

        // Use the convertFormula trait from Shadowrun 5E to avoid needing
        // eval() and calculate the total for the roll.
        $total = self::convertFormula(
            formula: $total,
            letter: 'F', // unused
            rating: 1 // unused
        );

        // Format the work string to show the original, the full math, and the
        // total.
        $work = $string . ' = ' . $work . ' = ' . $total;

        return (object)[
            'total' => $total,
            'rolls' => $rolls,
            'work' => $work,
        ];
    }

    /**
     * Given a string of dice notation and math, determine the highest possible
     * result.
     */
    public static function rollMax(string $string): int
    {
        $parts = self::getDynamicParts($string);
        foreach ($parts as $part) {
            [$dice, $pips] = self::getDiceAndPips($part);
            $string = str_replace(
                search: $part,
                replace: (string)($dice * $pips),
                subject: $string,
            );
        }

        // Use the convertFormula trait from Shadowrun 5E to avoid needing
        // eval() and calculate the total for the roll.
        $total = self::convertFormula(
            formula: $string,
            letter: 'Q', // unused
            rating: 0 // unused
        );

        return $total;
    }
}
