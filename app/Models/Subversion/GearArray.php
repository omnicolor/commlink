<?php

declare(strict_types=1);

namespace App\Models\Subversion;

use ArrayObject;
use TypeError;

/**
 * Collection of gear.
 * @extends ArrayObject<int, Gear>
 * @psalm-suppress UnusedClass
 */
class GearArray extends ArrayObject
{
    /**
     * Add an gear to the array.
     * @param ?int $index
     * @param Gear $gear
     * @psalm-suppress ParamNameMismatch
     * @throws TypeError
     */
    public function offsetSet($index = null, $gear = null): void
    {
        if ($gear instanceof Gear) {
            parent::offsetSet($index, $gear);
            return;
        }
        throw new TypeError('GearArray only accepts Gear objects');
    }
}
