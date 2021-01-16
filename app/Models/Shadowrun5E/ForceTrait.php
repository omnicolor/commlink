<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5E;

/**
 * Calculator for things that depend on a force or level, like spells and
 * complex forms.
 */
trait ForceTrait
{
    /**
     * Given a string involving a force or level calculation, replace the F or L
     * with the items's force or level and return what the value should be.
     *
     * Basically a complicated (but safe) way of avoiding eval(). Given
     * a string like "F/2" or "L+3", the letter to replace (F for magical
     * formulas, L for resonance formulas), and the force or level, will return
     * an integer for whatever the formula resolves to.
     *
     * @param string $formula Formula involving force or level
     * @param string $letter Letter to replace (L or F)
     * @param int $rating Force or level to use in formula
     * @return int
     */
    public function convertFormula(
        string $formula,
        string $letter,
        int $rating
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
        if (false === $components) {
            return 0;
        }
        while (false !== ($index = array_search('*', $components, true))) {
            array_splice(
                $components,
                $index - 1,
                3,
                (string)(
                    (int)$components[$index - 1] * (int)$components[$index + 1]
                )
            );
        }
        while (false !== ($index = array_search('/', $components, true))) {
            array_splice(
                $components,
                $index - 1,
                3,
                (string)(
                    (int)$components[$index - 1] / (int)$components[$index + 1]
                )
            );
        }
        while (false !== ($index = array_search('+', $components, true))) {
            array_splice(
                $components,
                $index - 1,
                3,
                (string)(
                    (int)$components[$index - 1] + (int)$components[$index + 1]
                )
            );
        }
        while (false !== ($index = array_search('-', $components, true))) {
            array_splice(
                $components,
                $index - 1,
                3,
                (string)(
                    (int)$components[$index - 1] - (int)$components[$index + 1]
                )
            );
        }
        return (int)current($components);
    }
}
