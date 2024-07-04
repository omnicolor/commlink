<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

enum VehicleModificationSlotType: string
{
    case Body = 'body';
    case Cosmetic = 'cosmetic';
    case Electromagnetic = 'electromagnetic';
    case PowerTrain = 'power-train';
    case Protection = 'protection';
    case Weapons = 'weapons';
}
