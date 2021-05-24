<?php

declare(strict_types=1);

namespace App\Models\CyberpunkRed;

/**
 * Collection of Weapons.
 * @extends \ArrayObject<int, Weapon>
 */
class WeaponArray extends \ArrayObject
{
    /**
     * Add a weapon to the array.
     * @param ?int $index
     * @param ?Weapon $weapon
     * @throws \TypeError
     */
    public function offsetSet($index = null, $weapon = null): void
    {
        if (!($weapon instanceof Weapon)) {
            throw new \TypeError('WeaponArray only accepts Weapon objects');
        }
        parent::offsetSet($index, $weapon);
    }
}
