<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5e;

enum VehicleModificationType: string
{
    case Equipment = 'equipment';
    case VehicleModification = 'vehicle-mod';
    case ModificationModification = 'modification-mod';
}
