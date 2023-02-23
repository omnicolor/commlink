<?php

declare(strict_types=1);

namespace App\Traits;

use function array_walk;

trait PrettifyRollsForSlack
{
    /**
     * Bold successes, strike out failures in the roll list.
     * @param array<int, int> $rolls
     * @return array<int, string>
     */
    public function prettifyRolls(array $rolls): array
    {
        array_walk($rolls, function (int | string &$value): void {
            if ($value >= 5) {
                $value = \sprintf('*%d*', $value);
            } elseif (1 == $value) {
                $value = \sprintf('~%d~', $value);
            }
        });
        // @phpstan-ignore-next-line
        return $rolls;
    }
}
