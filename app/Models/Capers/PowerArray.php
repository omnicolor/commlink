<?php

declare(strict_types=1);

namespace App\Models\Capers;

/**
 * Collection of Powers.
 * @extends \ArrayObject<int|string, Power>
 */
class PowerArray extends \ArrayObject
{
    /**
     * Add a power to the array.
     * @param int|null|string $index
     * @param Power $power
     * @throws \TypeError
     */
    public function offsetSet($index = null, $power = null): void
    {
        if ($power instanceof Power) {
            parent::offsetSet($index, $power);
            return;
        }
        throw new \TypeError('PowerArray only accepts Power objects');
    }
}
