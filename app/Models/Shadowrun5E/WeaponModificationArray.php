<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5E;

/**
 * Collection of weapon modifications.
 * @extends \ArrayObject<int, WeaponModification>
 */
class WeaponModificationArray extends \ArrayObject
{
    /**
     * Add a item to the array.
     * @param int|null $index
     * @param WeaponModification $mod
     * @throws \TypeError
     */
    public function offsetSet($index = null, $mod = null): void
    {
        if ($mod instanceof WeaponModification) {
            parent::offsetSet($index, $mod);
            return;
        }
        throw new \TypeError(
            'WeaponModificationArray only accepts WeaponModification objects'
        );
    }
}
