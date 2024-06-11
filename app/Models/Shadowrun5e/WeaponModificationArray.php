<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5e;

use ArrayObject;
use TypeError;

/**
 * Collection of weapon modifications, or null for the named slots in a weapon.
 * @extends ArrayObject<int|string, ?WeaponModification>
 */
class WeaponModificationArray extends ArrayObject
{
    /**
     * Add a item to the array.
     * @param ?WeaponModification $mod
     * @psalm-suppress ParamNameMismatch
     * @throws TypeError
     */
    public function offsetSet(mixed $index = null, $mod = null): void
    {
        if (null === $mod) {
            parent::offsetSet($index, null);
            return;
        }
        if ($mod instanceof WeaponModification) {
            parent::offsetSet($index, $mod);
            return;
        }
        throw new TypeError(
            'WeaponModificationArray only accepts WeaponModification objects'
        );
    }
}
