<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Enums;

use RangeException;

enum WeaponRange: string
{
    case HeavyPistol = 'heavy-pistol';
    case HoldOutPistol = 'hold-out-pistol';
    case LightPistol = 'light-pistol';
    case MachinePistol = 'machine-pistol';
    case Melee = 'melee';
    case Taser = 'taser';
    case Unknown = 'unknown';

    public function range(): string
    {
        return match ($this) {
            //['Melee Weapon', '???'],
            //WeaponClass::Cannon, '50/300/750/1200'],
            //WeaponClass::AssaultRifle, '25/150/350/550'],
            //WeaponClass::Bow, 'STR/STRx10/STRx30/STRx60'],
            //'Grenade', 'STRx2/STRx4/STRx6/STRx10'],
            //'Grenade Launcher', '5-50/100/150/500'],
            //WeaponClass::HeavyCrossbow, '14/45/120/180'],
            //'Heavy Machinegun', '40/250/750/1200'],
            WeaponRange::HeavyPistol => '5/20/40/60',
            WeaponRange::HoldOutPistol => '5/15/30/50',
            //'Light Crossbow', '6/24/60/120'],
            //'Light Machinegun', '25/200/400/800'],
            WeaponRange::LightPistol => '5/15/30/50',
            WeaponRange::MachinePistol => '5/15/30/50',
            WeaponRange::Melee => 'Reach',
            //'Medium Crossbow', '9/36/90/150'],
            //'Medium Machinegun', '40/250/750/1200'],
            //'Missile Launcher', '20-70*/150/450/1500'],
            //'Shotgun', '10/40/80/150'],
            //'Shotgun (flechette)', '15/30/45/60'],
            //'Sniper Rifle', '50/350/800/1500'],
            //'Submachine Gun', '10/40/80/150'],
            WeaponRange::Taser => '5/10/15/20',
            //'Throwing Weapon', 'STR/STRx2/STRx5/STRx7'],
            //'Thrown Knife', 'STR/STRx2/STRx3/STRx5'],
            WeaponRange::Unknown => 'Unknown',
        };
    }

    public function modifierForRange(int $distance_in_meters): int
    {
        return match ($this) {
            WeaponRange::HoldOutPistol,
            WeaponRange::LightPistol,
            WeaponRange::MachinePistol => match (true) {
                $distance_in_meters <= 5 => 0,
                $distance_in_meters <= 15 => -1,
                $distance_in_meters <= 30 => -3,
                $distance_in_meters <= 50 => -6,
                default => throw new RangeException(),
            },
            WeaponRange::HeavyPistol => match (true) {
                $distance_in_meters <= 5 => 0,
                $distance_in_meters <= 20 => -1,
                $distance_in_meters <= 40 => -3,
                $distance_in_meters <= 60 => -6,
                default => throw new RangeException(),
            },
            WeaponRange::Melee => match (true) {
                $distance_in_meters <= 2 => 0,
                default => throw new RangeException(),
            },
            WeaponRange::Taser => match (true) {
                $distance_in_meters <= 5 => 0,
                $distance_in_meters <= 10 => -1,
                $distance_in_meters <= 15 => -3,
                $distance_in_meters <= 20 => -6,
                default => throw new RangeException(),
            },
            WeaponRange::Unknown => throw new RangeException(),
        };
    }
}
