<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5e;

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
     * @param ?int $index
     * @param Weapon $weapon
     * @throws TypeError if the object assigned isn't a weapon
     */
    public function offsetSet($index = null, $weapon = null): void
    {
        if ($weapon instanceof Weapon) {
            parent::offsetSet($index, $weapon);
            return;
        }
        throw new TypeError('WeaponArray only accepts Weapon objects');
    }
}
