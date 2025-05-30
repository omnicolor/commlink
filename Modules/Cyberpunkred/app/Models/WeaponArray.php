<?php

declare(strict_types=1);

namespace Modules\Cyberpunkred\Models;

use ArrayObject;
use Override;
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
     * @throws TypeError
     */
    #[Override]
    public function offsetSet(mixed $index = null, $weapon = null): void
    {
        if ($weapon instanceof Weapon) {
            parent::offsetSet($index, $weapon);
            return;
        }
        throw new TypeError('WeaponArray only accepts Weapon objects');
    }
}
