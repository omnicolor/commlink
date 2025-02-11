<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use ArrayObject;
use Override;
use TypeError;

/**
 * Collection of armor modifications, which can either be ArmorModifications or
 * GearModifications.
 * @extends ArrayObject<int, ArmorModification|GearModification>
 */
final class ArmorModificationArray extends ArrayObject
{
    /**
     * Add an armor modification to the array.
     * @param ArmorModification|GearModification $mod
     * @throws TypeError
     */
    #[Override]
    public function offsetSet(mixed $index = null, $mod = null): void
    {
        if (
            !($mod instanceof ArmorModification)
            && !($mod instanceof GearModification)
        ) {
            throw new TypeError(
                'ArmorModificationArray only accepts Armor- or GearModification objects'
            );
        }
        parent::offsetSet($index, $mod);
    }
}
