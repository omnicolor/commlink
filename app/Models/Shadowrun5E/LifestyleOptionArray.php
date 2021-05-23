<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5E;

/**
 * Collection of Lifestyle Options.
 * @extends \ArrayObject<int, LifestyleOption>
 */
class LifestyleOptionArray extends \ArrayObject
{
    /**
     * Add an option to the array.
     * @param ?int $index
     * @param LifestyleOption $option
     * @throws \TypeError
     */
    public function offsetSet($index = null, $option = null): void
    {
        if ($option instanceof LifestyleOption) {
            parent::offsetSet($index, $option);
            return;
        }
        throw new \TypeError(
            'LifestyleOptionArray only accepts LifestyleOption objects'
        );
    }
}
