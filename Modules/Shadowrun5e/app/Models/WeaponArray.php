<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use ArrayObject;
use TypeError;

/**
 * Collection of weapons.
 * @extends ArrayObject<int, Weapon>
 */
class WeaponArray extends ArrayObject
{
    /**
     * Force the array to only accept Weapon objects.
     * @param Weapon $weapon
     * @throws TypeError if the object assigned isn't a weapon
     */
    public function offsetSet(mixed $index = null, $weapon = null): void
    {
        if ($weapon instanceof Weapon) {
            parent::offsetSet($index, $weapon);
            return;
        }
        throw new TypeError('WeaponArray only accepts Weapon objects');
    }
}
