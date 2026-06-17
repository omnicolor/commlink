<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Enums;

enum SpellRange: string
{
    case Touch = 'Touch';
    case LineOfSight = 'LOS';
    case LineOfSightArea = 'LOA(A)';
    case Special = 'Special';
}
