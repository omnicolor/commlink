<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

enum VehicleModificationType: string
{
    case Equipment = 'equipment';
    case VehicleModification = 'vehicle-mod';
    case ModificationModification = 'modification-mod';
}
