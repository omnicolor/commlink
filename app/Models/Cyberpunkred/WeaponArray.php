<?php

declare(strict_types=1);

namespace App\Models\Cyberpunkred;

use ArrayObject;
use TypeError;

/**
 * Collection of Weapons.
 * @extends ArrayObject<int, Weapon>
 */
class WeaponArray extends ArrayObject
{
    /**
     * Add a weapon to the array.
     * @param ?Weapon $weapon
     * @psalm-suppress ParamNameMismatch
     * @throws TypeError
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
