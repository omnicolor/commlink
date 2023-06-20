<?php

declare(strict_types=1);

namespace App\Models\Transformers;

use ArrayObject;
use TypeError;

/**
 * Collection of Weapons.
 * @extends ArrayObject<int, Weapon>
 */
class WeaponArray extends ArrayObject
{
    /**
     * @param int|null|string $index
     * @param Weapon $weapon
     * @throws TypeError
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
