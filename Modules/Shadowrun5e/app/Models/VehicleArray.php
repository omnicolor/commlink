<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use ArrayObject;
use Override;
use TypeError;

/**
 * Fleet of vehicles.
 * @extends ArrayObject<int, Vehicle>
 */
final class VehicleArray extends ArrayObject
{
    /**
     * Add a vehicle to the array.
     * @param Vehicle $vehicle
     * @throws TypeError
     */
    #[Override]
    public function offsetSet(mixed $index = null, $vehicle = null): void
    {
        if ($vehicle instanceof Vehicle) {
            parent::offsetSet($index, $vehicle);
            return;
        }
        throw new TypeError('VehicleArray only accepts Vehicle objects');
    }
}
