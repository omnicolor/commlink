<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use ArrayObject;
use Override;
use TypeError;

/**
 * Type-safe collection of Gear.
 * @extends ArrayObject<int, Gear>
 */
final class GearArray extends ArrayObject
{
    /**
     * Adds some gear to the array.
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
