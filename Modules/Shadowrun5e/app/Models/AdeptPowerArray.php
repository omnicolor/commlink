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
final class AdeptPowerArray extends ArrayObject
{
    /**
     * Add a power to the array.
     * @param AdeptPower $value
     * @throws TypeError
     */
    #[Override]
    public function offsetSet(mixed $key = null, $value = null): void
    {
        if ($value instanceof AdeptPower) {
            parent::offsetSet($key, $value);
            return;
        }
        throw new TypeError('AdeptPowerArray only accepts AdeptPower objects');
    }
}
