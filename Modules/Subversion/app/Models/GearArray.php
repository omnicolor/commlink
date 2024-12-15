<?php

declare(strict_types=1);

namespace Modules\Subversion\Models;

use ArrayObject;
use TypeError;

/**
 * Collection of gear.
 * @extends ArrayObject<int, Gear>
 */
class GearArray extends ArrayObject
{
    /**
     * Add an gear to the array.
     * @param ?int $index
     * @param Gear $gear
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
