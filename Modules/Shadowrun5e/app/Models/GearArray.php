<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use ArrayObject;
use TypeError;

/**
 * Type-safe collection of Gear.
 * @extends ArrayObject<int, Gear>
 */
class GearArray extends ArrayObject
{
    /**
     * Adds some gear to the array.
     * @param Gear $gear
     * @psalm-suppress ParamNameMismatch
     * @throws TypeError
     */
    public function offsetSet(mixed $index = null, $gear = null): void
    {
        if ($gear instanceof Gear) {
            parent::offsetSet($index, $gear);
            return;
        }
        throw new TypeError('GearArray only accepts Gear objects');
    }
}
