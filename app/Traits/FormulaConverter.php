<?php

declare(strict_types=1);

namespace App\Traits;

use function array_search;
use function array_splice;
use function current;
use function preg_split;
use function str_replace;

use const PREG_SPLIT_DELIM_CAPTURE;
use const PREG_SPLIT_NO_EMPTY;

/**
 * Calculator for things that depend on a force or level, like spells and
 * complex forms.
 */
trait FormulaConverter
{
    /**
     * Given a string involving a formula with a letter that needs to be
     * replaced with a number and return what the value should be.
     *
     * Basically a complicated (but safe) way of avoiding eval(). Given
     * a string like "F/2" or "L+3", the letter to replace (F for magical
     * formulas or L for resonance formulas in Shadowrun for example),
     * and the force or level, will return an integer for whatever the
     * formula resolves to.
     */
    public static function convertFormula(
        string $formula,
        string $letter,
        int $rating,
    ): int {
        // If $formula = "F+3", $letter = "F", and $rating = 6, change it to
        // "6+3"
        $formula = str_replace($letter, (string)$rating, $formula);
        $components = preg_split(
            '~(?<=\d)([*/+-])~',
            $formula,
            -1,
            PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE
        );
        // @codeCoverageIgnoreStart
        // The preg_split() function should only return false if the regular
        // expression itself is invalid. The above expression is valid, so this
        // can't happen. We're just checking this for code correctness (and to
        // keep PHPStan happy).
        if (false === $components) {
            return 0;
        }
        // @codeCoverageIgnoreEnd

        // Process multiplication and division left to right.
        $multiplyIndex = array_search('*', $components, true);
        $divideIndex = array_search('/', $components, true);
        while (false !== $multiplyIndex || false !== $divideIndex) {
            if (false !== $multiplyIndex && (false === $divideIndex || $multiplyIndex < $divideIndex)) {
                $index = (int)$multiplyIndex;
                $result = (int)$components[$index - 1] * (int)$components[$index + 1];
            } else {
                $index = (int)$divideIndex;
                $result = (int)$components[$index - 1] / (int)$components[$index + 1];
            }
            array_splice($components, $index - 1, 3, (string)$result);
            $multiplyIndex = array_search('*', $components, true);
            $divideIndex = array_search('/', $components, true);
        }

        // Process addition and subtraction left to right.
        $plusIndex = array_search('+', $components, true);
        $minusIndex = array_search('-', $components, true);
        while (false !== $plusIndex || false !== $minusIndex) {
            if (false !== $plusIndex && (false === $minusIndex || $plusIndex < $minusIndex)) {
                $index = (int)$plusIndex;
                $result = (int)$components[$index - 1] + (int)$components[$index + 1];
            } else {
                $index = (int)$minusIndex;
                $result = (int)$components[$index - 1] - (int)$components[$index + 1];
            }
            array_splice($components, $index - 1, 3, (string)$result);
            $plusIndex = array_search('+', $components, true);
            $minusIndex = array_search('-', $components, true);
        }

        return (int)current($components);
    }
}
