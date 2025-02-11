<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Enums;

use RangeException;

/**
 * @codeCoverageIgnore
 */
enum WeaponRange: string
{
    case AssaultRifle = 'assault-rifle';
    case Bow = 'bow';
    case Cannon = 'cannon';
    case Grenade = 'grenade';
    case GrenadeLauncher = 'grenade-launcher';
    case HeavyCrossbow = 'heavy-crossbow';
    case HeavyMachinegun = 'heavy-machinegun';
    case HeavyPistol = 'heavy-pistol';
    case HoldOutPistol = 'hold-out-pistol';
    case LightCrossbow = 'light-crossbow';
    case LightMachinegun = 'light-machinegun';
    case LightPistol = 'light-pistol';
    case MachinePistol = 'machine-pistol';
    case MediumCrossbow = 'medium-crossbow';
    case MediumMachinegun = 'medium-machinegun';
    case Melee = 'melee';
    case MissileLauncher = 'missile-launcher';
    case Shotgun = 'shotgun';
    case ShotgunFlechette = 'shotgun-flechette';
    case SniperRifle = 'sniper-rifle';
    case SubmachineGun = 'submachine-gun';
    case Taser = 'taser';
    case ThrowingWeapon = 'throwing-weapon';
    case ThrownKnife = 'thrown-knife';
    case Unknown = 'unknown';

    public function range(): string
    {
        return match ($this) {
            WeaponRange::Cannon => '50/300/750/1200',
            WeaponRange::AssaultRifle => '25/150/350/550',
            WeaponRange::Bow => 'STR/STRx10/STRx30/STRx60',
            WeaponRange::Grenade => 'STRx2/STRx4/STRx6/STRx10',
            WeaponRange::GrenadeLauncher => '5-50/100/150/500',
            WeaponRange::HeavyCrossbow => '14/45/120/180',
            WeaponRange::HeavyMachinegun => '40/250/750/1200',
            WeaponRange::HeavyPistol => '5/20/40/60',
            WeaponRange::HoldOutPistol => '5/15/30/50',
            WeaponRange::LightCrossbow => '6/24/60/120',
            WeaponRange::LightMachinegun => '25/200/400/800',
            WeaponRange::LightPistol => '5/15/30/50',
            WeaponRange::MachinePistol => '5/15/30/50',
            WeaponRange::Melee => 'Reach',
            WeaponRange::MediumCrossbow => '9/36/90/150',
            WeaponRange::MediumMachinegun => '40/250/750/1200',
            WeaponRange::MissileLauncher => '20-70*/150/450/1500',
            WeaponRange::Shotgun => '10/40/80/150',
            WeaponRange::ShotgunFlechette => '15/30/45/60',
            WeaponRange::SniperRifle => '50/350/800/1500',
            WeaponRange::SubmachineGun => '10/40/80/150',
            WeaponRange::Taser => '5/10/15/20',
            WeaponRange::ThrowingWeapon => 'STR/STRx2/STRx5/STRx7',
            WeaponRange::ThrownKnife => 'STR/STRx2/STRx3/STRx5',
            WeaponRange::Unknown => 'Unknown',
        };
    }

    public function modifierForRange(
        int $distance_in_meters,
        int $strength,
    ): int {
        return match ($this) {
            WeaponRange::AssaultRifle => match (true) {
                $distance_in_meters <= 25 => 0,
                $distance_in_meters <= 150 => -1,
                $distance_in_meters <= 350 => -3,
                $distance_in_meters <= 550 => -6,
                default => throw new RangeException(),
            },
            WeaponRange::Bow => match (true) {
                $distance_in_meters <= $strength => 0,
                $distance_in_meters <= $strength * 10 => -1,
                $distance_in_meters <= $strength * 30 => -3,
                $distance_in_meters <= $strength * 60 => -6,
                default => throw new RangeException(),
            },
            WeaponRange::Cannon => match (true) {
                $distance_in_meters <= 50 => 0,
                $distance_in_meters <= 300 => -1,
                $distance_in_meters <= 750 => -3,
                $distance_in_meters <= 1200 => -6,
                default => throw new RangeException(),
            },
            WeaponRange::Grenade => match (true) {
                $distance_in_meters <= $strength * 2 => 0,
                $distance_in_meters <= $strength * 4 => -1,
                $distance_in_meters <= $strength * 6 => -3,
                $distance_in_meters <= $strength * 10 => -6,
                default => throw new RangeException(),
            },
            WeaponRange::GrenadeLauncher => match (true) {
                $distance_in_meters <= 5 => throw new RangeException(),
                $distance_in_meters <= 50 => 0,
                $distance_in_meters <= 100 => -1,
                $distance_in_meters <= 150 => -3,
                $distance_in_meters <= 500 => -6,
                default => throw new RangeException(),
            },
            WeaponRange::HeavyCrossbow => match (true) {
                $distance_in_meters <= 14 => 0,
                $distance_in_meters <= 45 => -1,
                $distance_in_meters <= 120 => -3,
                $distance_in_meters <= 180 => -6,
                default => throw new RangeException(),
            },
            WeaponRange::HeavyMachinegun => match (true) {
                $distance_in_meters <= 40 => 0,
                $distance_in_meters <= 250 => -1,
                $distance_in_meters <= 750 => -3,
                $distance_in_meters <= 1200 => -6,
                default => throw new RangeException(),
            },
            WeaponRange::HeavyPistol => match (true) {
                $distance_in_meters <= 5 => 0,
                $distance_in_meters <= 20 => -1,
                $distance_in_meters <= 40 => -3,
                $distance_in_meters <= 60 => -6,
                default => throw new RangeException(),
            },
            WeaponRange::HoldOutPistol => match (true) {
                $distance_in_meters <= 5 => 0,
                $distance_in_meters <= 15 => -1,
                $distance_in_meters <= 30 => -3,
                $distance_in_meters <= 50 => -6,
                default => throw new RangeException(),
            },
            WeaponRange::LightCrossbow => match (true) {
                $distance_in_meters <= 6 => 0,
                $distance_in_meters <= 24 => -1,
                $distance_in_meters <= 60 => -3,
                $distance_in_meters <= 120 => -6,
                default => throw new RangeException(),
            },
            WeaponRange::LightPistol => match (true) {
                $distance_in_meters <= 5 => 0,
                $distance_in_meters <= 15 => -1,
                $distance_in_meters <= 30 => -3,
                $distance_in_meters <= 50 => -6,
                default => throw new RangeException(),
            },
            WeaponRange::LightMachinegun => match (true) {
                $distance_in_meters <= 25 => 0,
                $distance_in_meters <= 200 => -1,
                $distance_in_meters <= 400 => -3,
                $distance_in_meters <= 800 => -6,
                default => throw new RangeException(),
            },
            WeaponRange::MachinePistol => match (true) {
                $distance_in_meters <= 5 => 0,
                $distance_in_meters <= 15 => -1,
                $distance_in_meters <= 30 => -3,
                $distance_in_meters <= 50 => -6,
                default => throw new RangeException(),
            },
            WeaponRange::MediumCrossbow => match (true) {
                $distance_in_meters <= 9 => 0,
                $distance_in_meters <= 36 => -1,
                $distance_in_meters <= 90 => -3,
                $distance_in_meters <= 150 => -6,
                default => throw new RangeException(),
            },
            WeaponRange::MediumMachinegun => match (true) {
                $distance_in_meters <= 40 => 0,
                $distance_in_meters <= 250 => -1,
                $distance_in_meters <= 750 => -3,
                $distance_in_meters <= 1200 => -6,
                default => throw new RangeException(),
            },
            WeaponRange::Melee => match (true) {
                $distance_in_meters <= 2 => 0,
                default => throw new RangeException(),
            },
            WeaponRange::MissileLauncher => match (true) {
                $distance_in_meters <= 20 => throw new RangeException(),
                $distance_in_meters <= 70 => 0,
                $distance_in_meters <= 150 => -1,
                $distance_in_meters <= 450 => -3,
                $distance_in_meters <= 1500 => -6,
                default => throw new RangeException(),
            },
            WeaponRange::Shotgun => match (true) {
                $distance_in_meters <= 10 => 0,
                $distance_in_meters <= 40 => -1,
                $distance_in_meters <= 80 => -3,
                $distance_in_meters <= 150 => -6,
                default => throw new RangeException(),
            },
            WeaponRange::ShotgunFlechette => match (true) {
                $distance_in_meters <= 15 => 0,
                $distance_in_meters <= 30 => -1,
                $distance_in_meters <= 45 => -3,
                $distance_in_meters <= 60 => -6,
                default => throw new RangeException(),
            },
            WeaponRange::SniperRifle => match (true) {
                $distance_in_meters <= 50 => 0,
                $distance_in_meters <= 350 => -1,
                $distance_in_meters <= 800 => -3,
                $distance_in_meters <= 1500 => -6,
                default => throw new RangeException(),
            },
            WeaponRange::SubmachineGun => match (true) {
                $distance_in_meters <= 10 => 0,
                $distance_in_meters <= 40 => -1,
                $distance_in_meters <= 80 => -3,
                $distance_in_meters <= 150 => -6,
                default => throw new RangeException(),
            },
            WeaponRange::Taser => match (true) {
                $distance_in_meters <= 5 => 0,
                $distance_in_meters <= 10 => -1,
                $distance_in_meters <= 15 => -3,
                $distance_in_meters <= 20 => -6,
                default => throw new RangeException(),
            },
            WeaponRange::ThrowingWeapon => match (true) {
                $distance_in_meters <= $strength => 0,
                $distance_in_meters <= $strength * 2 => -1,
                $distance_in_meters <= $strength * 5 => -3,
                $distance_in_meters <= $strength * 7 => -6,
                default => throw new RangeException(),
            },
            WeaponRange::ThrownKnife => match (true) {
                $distance_in_meters <= $strength => 0,
                $distance_in_meters <= $strength * 2 => -1,
                $distance_in_meters <= $strength * 3 => -3,
                $distance_in_meters <= $strength * 5 => -6,
                default => throw new RangeException(),
            },
            WeaponRange::Unknown => throw new RangeException(),
        };
    }
}
