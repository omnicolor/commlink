<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Enums;

enum WeaponClass: string
{
    case AssaultRifle = 'assault-rifle';
    case Blade = 'blade';
    case Bow = 'bow';
    case Cannon = 'cannon';
    case Club = 'club';
    case Crossbow = 'crossbow';
    case GrenadeLauncher = 'grenade-launcher';
    case HeavyPistol = 'heavy-pistol';
    case HoldOutPistol = 'hold-out-pistol';
    case LightPistol = 'light-pistol';
    case MachineGun = 'machine-gun';
    case MachinePistol = 'machine-pistol';
    case MissileLauncher = 'missile-launcher';
    case OtherMelee = 'other-melee';
    case Shotgun = 'shotgun';
    case SniperRifle = 'sniper-rifle';
    case SpecialWeapon = 'special-weapon';
    case SubmachineGun = 'submachine-gun';
    case Taser = 'taser';
    case ThrowingWeapon = 'throwing-weapon';

    public function name(): string
    {
        return match ($this) {
            WeaponClass::AssaultRifle => 'Assault rifle',
            WeaponClass::Blade => 'Blade',
            WeaponClass::Bow => 'Bow',
            WeaponClass::Cannon => 'Cannon',
            WeaponClass::Club => 'Club',
            WeaponClass::Crossbow => 'Crossbow',
            WeaponClass::GrenadeLauncher => 'Grenade launcher',
            WeaponClass::HeavyPistol => 'Heavy pistol',
            WeaponClass::HoldOutPistol => 'Hold-out pistol',
            WeaponClass::LightPistol => 'Light pistol',
            WeaponClass::MachineGun => 'Machine gun',
            WeaponClass::MachinePistol => 'Machine pistol',
            WeaponClass::MissileLauncher => 'Missile launcher',
            WeaponClass::OtherMelee => 'Other Melee',
            WeaponClass::Shotgun => 'Shotgun',
            WeaponClass::SniperRifle => 'Sniper rifle',
            WeaponClass::SpecialWeapon => 'Special weapon',
            WeaponClass::SubmachineGun => 'Submachine gun',
            WeaponClass::Taser => 'Taser',
            WeaponClass::ThrowingWeapon => 'Throwing Weapon',
        };
    }
}
