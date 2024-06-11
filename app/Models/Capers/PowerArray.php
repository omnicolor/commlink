<?php

declare(strict_types=1);

namespace App\Models\Capers;

use ArrayObject;
use TypeError;

/**
 * Collection of Powers.
 * @extends ArrayObject<int|string, Power>
 */
class PowerArray extends ArrayObject
{
    /**
     * Add a power to the array.
     * @param Power $power
     * @psalm-suppress ParamNameMismatch
     * @throws TypeError
     */
    public function offsetSet(mixed $index = null, $power = null): void
    {
        if ($power instanceof Power) {
            parent::offsetSet($index, $power);
            return;
        }
        throw new TypeError('PowerArray only accepts Power objects');
    }
}
