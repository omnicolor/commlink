<?php

declare(strict_types=1);

namespace Modules\Capers\Models;

use ArrayObject;
use Override;
use TypeError;

/**
 * Collection of Gear.
 * @extends ArrayObject<int|string, Gear>
 */
class GearArray extends ArrayObject
{
    /**
     * Add a gear item to the array.
     * @param Gear $gear
     * @throws TypeError
     */
    #[Override]
    public function offsetSet(mixed $index = null, $gear = null): void
    {
        if ($gear instanceof Gear) {
            parent::offsetSet($index, $gear);
            return;
        }
        throw new TypeError('GearArray only accepts Gear objects');
    }
}
