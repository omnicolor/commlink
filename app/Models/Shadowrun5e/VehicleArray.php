<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5e;

use ArrayObject;
use TypeError;

/**
 * Fleet of vehicles.
 * @extends ArrayObject<int, Vehicle>
 */
class VehicleArray extends ArrayObject
{
    /**
     * Add a vehicle to the array.
     * @param Vehicle $vehicle
     * @psalm-suppress ParamNameMismatch
     * @throws TypeError
     */
    public function offsetSet(mixed $index = null, $vehicle = null): void
    {
        if ($vehicle instanceof Vehicle) {
            parent::offsetSet($index, $vehicle);
            return;
        }
        throw new TypeError('VehicleArray only accepts Vehicle objects');
    }
}
