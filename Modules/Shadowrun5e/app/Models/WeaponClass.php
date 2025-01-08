<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

enum WeaponClass: string
{
    case Blade = 'blade';
    case Club = 'club';
    case OtherMelee = 'other-melee';
    case Bow = 'bow';
    case Crossbow = 'crossbow';
    case ThrowingWeapon = 'throwing-weapon';
    case Taser = 'taser';
    case HoldOutPistol = 'hold-out-pistol';
    case LightPistol = 'light-pistol';
    case HeavyPistol = 'heavy-pistol';
    case MachinePistol = 'machine-pistol';
    case SubmachineGun = 'submachine-gun';
    case AssaultRifle = 'assault-rifle';
    case SniperRifle = 'sniper-rifle';
    case Shotgun = 'shotgun';
    case SpecialWeapon = 'special-weapon';
    case MachineGun = 'machine-gun';
    case Cannon = 'cannon';
    case MissileLauncher = 'missile-launcher';

    public function name(): string
    {
        return match ($this) {
            WeaponClass::Blade => 'Blade',
            WeaponClass::Club => 'Club',
            WeaponClass::OtherMelee => 'Other Melee',
            WeaponClass::Bow => 'Bow',
            WeaponClass::Crossbow => 'Crossbow',
            WeaponClass::ThrowingWeapon => 'Throwing Weapon',
            WeaponClass::Taser => 'Taser',
            WeaponClass::HoldOutPistol => 'Hold-out pistol',
            WeaponClass::LightPistol => 'Light pistol',
            WeaponClass::HeavyPistol => 'Heavy pistol',
            WeaponClass::MachinePistol => 'Machine pistol',
            WeaponClass::SubmachineGun => 'Submachine gun',
            WeaponClass::AssaultRifle => 'Assault rifle',
            WeaponClass::SniperRifle => 'Sniper rifle',
            WeaponClass::Shotgun => 'Shotgun',
            WeaponClass::SpecialWeapon => 'Special weapon',
            WeaponClass::MachineGun => 'Machine gun',
            WeaponClass::MissileLauncher => 'Missile launcher',
            WeaponClass::Cannon => 'Cannon',
        };
    }
}
