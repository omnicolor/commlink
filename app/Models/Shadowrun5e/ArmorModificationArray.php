<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5e;

/**
 * Collection of armor modifications, which can either be ArmorModifications or
 * GearModifications.
 * @extends \ArrayObject<int, ArmorModification|GearModification>
 */
class ArmorModificationArray extends \ArrayObject
{
    /**
     * Add an armor modification to the array.
     * @param int|null $index
     * @param ArmorModification|GearModification $mod
     * @throws \TypeError
     */
    public function offsetSet($index = null, $mod = null): void
    {
        if (
            !($mod instanceof ArmorModification)
            && !($mod instanceof GearModification)
        ) {
            throw new \TypeError(
                'ArmorModificationArray only accepts Armor- or GearModification objects'
            );
        }
        parent::offsetSet($index, $mod);
    }
}
