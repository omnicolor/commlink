<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Enums;

enum CapacityCategory: string
{
    case Clip = 'c';
    case Magazine = 'm';
    case Belt = 'b';
    case BreakAction = 'a';
    case Cylinder = 'cy';
    case MuzzleLoader = 'z';
}
