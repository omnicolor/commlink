<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5E;

/**
 * Collection of weapon modifications, or null for the named slots in a weapon.
 * @extends \ArrayObject<int|string, ?WeaponModification>
 */
class WeaponModificationArray extends \ArrayObject
{
    /**
     * Add a item to the array.
     * @param null|int|string $index
     * @param ?WeaponModification $mod
     * @throws \TypeError
     */
    public function offsetSet($index = null, $mod = null): void
    {
        if (null === $mod) {
            parent::offsetSet($index, null);
            return;
        }
        if ($mod instanceof WeaponModification) {
            parent::offsetSet($index, $mod);
            return;
        }
        throw new \TypeError(
            'WeaponModificationArray only accepts WeaponModification objects'
        );
    }
}
