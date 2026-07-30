<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Enums;

enum AdeptPowerActivation: string
{
    case MajorAction = 'major';
    case MinorAction = 'minor';
    case PassiveAction = 'passive';
}
