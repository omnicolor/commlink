<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5E;

/**
 * Collection of gear modifications.
 * @extends \ArrayObject<int, GearModification>
 */
class GearModificationArray extends \ArrayObject
{
    /**
     * Add a item to the array.
     * @param int|null $index
     * @param GearModification $mod
     * @throws \TypeError
     */
    public function offsetSet($index = null, $mod = null): void
    {
        if ($mod instanceof GearModification) {
            parent::offsetSet($index, $mod);
            return;
        }
        throw new \TypeError(
            'GearModificationArray only accepts GearModification objects'
        );
    }
}
