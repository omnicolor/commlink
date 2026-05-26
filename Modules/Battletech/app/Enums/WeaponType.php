<?php

declare(strict_types=1);

namespace Modules\Battletech\Enums;

enum WeaponType: string
{
    case Ballistic = 'B';
    case Energy = 'E';
    case Explosive = 'X';
    case Melee = 'M';
    case Special = 'S';
}
