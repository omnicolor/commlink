<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Enums;

enum FiringMode: string
{
    case SingleShot = 'SS';
    case SemiAutomatic = 'SA';
    case BurstFire = 'BF';
    case FullyAutomatic = 'FA';
}
