<?php

declare(strict_types=1);

namespace Modules\Stillfleet\Enums;

enum WeaponType: string
{
    case Grenade = 'grenade';
    case Melee = 'melee';
    case Missile = 'missile';
}
