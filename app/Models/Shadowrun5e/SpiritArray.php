<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5e;

use ArrayObject;
use TypeError;

/**
 * Collection of spirits.
 * @extends ArrayObject<int, Spirit>
 */
class SpiritArray extends ArrayObject
{
    /**
     * Add a spirit to the array.
     * @param Spirit $spirit
     * @psalm-suppress ParamNameMismatch
     * @throws TypeError
     */
    public function offsetSet(mixed $index = null, $spirit = null): void
    {
        if ($spirit instanceof Spirit) {
            parent::offsetSet($index, $spirit);
            return;
        }
        throw new TypeError('SpiritArray only accepts Spirit objects');
    }
}
