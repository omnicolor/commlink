<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use ArrayObject;
use Override;
use TypeError;

/**
 * Collection of vehicle modifications.
 * @extends ArrayObject<int, VehicleModification>
 */
final class VehicleModificationArray extends ArrayObject
{
    /**
     * Add a modification to the array.
     * @param VehicleModification $mod
     * @throws TypeError
     */
    #[Override]
    public function offsetSet(mixed $index = null, $mod = null): void
    {
        if ($mod instanceof VehicleModification) {
            parent::offsetSet($index, $mod);
            return;
        }
        throw new TypeError(
            'VehicleModificationArray only accepts VehicleModification objects'
        );
    }
}
