<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5E;

/**
 * Collection of vehicle modifications.
 * @extends \ArrayObject<int, VehicleModification>
 */
class VehicleModificationArray extends \ArrayObject
{
    /**
     * Add a modification to the array.
     * @param ?int $index
     * @param VehicleModification $mod
     * @throws \TypeError
     */
    public function offsetSet($index = null, $mod = null): void
    {
        if ($mod instanceof VehicleModification) {
            parent::offsetSet($index, $mod);
            return;
        }
        throw new \TypeError(
            'VehicleModificationArray only accepts VehicleModification objects'
        );
    }
}
