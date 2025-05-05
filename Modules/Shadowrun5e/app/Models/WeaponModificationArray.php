<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use ArrayObject;
use Override;
use TypeError;

/**
 * Collection of weapon modifications, or null for the named slots in a weapon.
 * @extends ArrayObject<int|string, ?WeaponModification>
 */
final class WeaponModificationArray extends ArrayObject
{
    /**
     * Add an item to the array.
     * @param WeaponModification|null $mod
     * @throws TypeError
     */
    #[Override]
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
