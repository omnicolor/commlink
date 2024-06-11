<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5e;

use ArrayObject;
use TypeError;

/**
 * Collection of gear modifications.
 * @extends ArrayObject<int, GearModification>
 */
class GearModificationArray extends ArrayObject
{
    /**
     * Add a item to the array.
     * @param GearModification $mod
     * @psalm-suppress ParamNameMismatch
     * @throws TypeError
     */
    public function offsetSet(mixed $index = null, $mod = null): void
    {
        if ($mod instanceof GearModification) {
            parent::offsetSet($index, $mod);
            return;
        }
        throw new TypeError(
            'GearModificationArray only accepts GearModification objects'
        );
    }
}
