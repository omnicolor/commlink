<?php

declare(strict_types=1);

namespace Modules\Stillfleet\Enums;

enum AdvancedPowersCategory: string
{
    case Communications = 'communications';
    case Immerphysics = 'immerphysics';
    case Nanofluidics = 'nanofluidics';
    case Operations = 'operations';
    case Tactics = 'tactics';
}
