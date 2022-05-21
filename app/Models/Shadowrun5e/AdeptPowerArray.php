<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5e;

/**
 * Collection of adept powers.
 * @extends \ArrayObject<int, AdeptPower>
 */
class AdeptPowerArray extends \ArrayObject
{
    /**
     * Add a power to the array.
     * @param ?int $index
     * @param AdeptPower $power
     * @throws \TypeError
     */
    public function offsetSet($index = null, $power = null): void
    {
        if ($power instanceof AdeptPower) {
            parent::offsetSet($index, $power);
            return;
        }
        throw new \TypeError('AdeptPowerArray only accepts AdeptPower objects');
    }
}
