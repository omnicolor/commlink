<?php

declare(strict_types=1);

namespace App\Models\Capers;

/**
 * Collection of Gear.
 * @extends \ArrayObject<int, Gear>
 */
class GearArray extends \ArrayObject
{
    /**
     * Add a gear item to the array.
     * @param ?mixed $index
     * @param Gear $gear
     * @throws \TypeError
     */
    public function offsetSet($index = null, $gear = null): void
    {
        if ($gear instanceof Gear) {
            parent::offsetSet($index, $gear);
            return;
        }
        throw new \TypeError('GearArray only accepts Gear objects');
    }
}
