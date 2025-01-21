<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use ArrayObject;
use Override;
use TypeError;

/**
 * Collection of adept powers.
 * @extends ArrayObject<int, AdeptPower>
 */
class AdeptPowerArray extends ArrayObject
{
    /**
     * Add a power to the array.
     * @param AdeptPower $power
     * @throws TypeError
     */
    #[Override]
    public function offsetSet(mixed $index = null, $power = null): void
    {
        if ($power instanceof AdeptPower) {
            parent::offsetSet($index, $power);
            return;
        }
        throw new TypeError('AdeptPowerArray only accepts AdeptPower objects');
    }
}
