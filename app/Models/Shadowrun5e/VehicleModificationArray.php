<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5e;

use ArrayObject;
use TypeError;

/**
 * Collection of vehicle modifications.
 * @extends ArrayObject<int, VehicleModification>
 */
class VehicleModificationArray extends ArrayObject
{
    /**
     * Add a modification to the array.
     * @param ?int $index
     * @param VehicleModification $mod
     * @psalm-suppress ParamNameMismatch
     * @throws TypeError
     */
    public function offsetSet($index = null, $mod = null): void
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
