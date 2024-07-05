<?php

declare(strict_types=1);

namespace Modules\Transformers\Models;

use ArrayObject;
use TypeError;

/**
 * Collection of Weapons.
 * @extends ArrayObject<int, Weapon>
 */
class WeaponArray extends ArrayObject
{
    /**
     * @param int|null|string $offset
     * @param Weapon $value
     * @throws TypeError
     */
    public function offsetSet($offset = null, $value = null): void
    {
        if ($value instanceof Weapon) {
            parent::offsetSet($offset, $value);
            return;
        }
        throw new TypeError('WeaponArray only accepts Weapon objects');
    }
}
