<?php

declare(strict_types=1);

namespace Modules\Transformers\Models;

use ArrayObject;
use Override;
use TypeError;

/**
 * Collection of Weapons.
 * @extends ArrayObject<int|string, Weapon>
 */
class WeaponArray extends ArrayObject
{
    /**
     * @param int|string|null $key
     * @param Weapon $value
     * @throws TypeError
     */
    #[Override]
    public function offsetSet($key = null, $value = null): void
    {
        if ($value instanceof Weapon) {
            parent::offsetSet($key, $value);
            return;
        }
        throw new TypeError('WeaponArray only accepts Weapon objects');
    }
}
