<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use ArrayObject;
use Override;
use TypeError;

/**
 * Collection of gear modifications.
 * @extends ArrayObject<int, GearModification>
 */
final class GearModificationArray extends ArrayObject
{
    /**
     * Add an item to the array.
     * @param GearModification $mod
     * @throws TypeError
     */
    #[Override]
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
