<?php

declare(strict_types=1);

namespace Modules\Battletech\Tests\Feature\Models;

use Modules\Battletech\Enums\AvailabilityRating;
use Modules\Battletech\Enums\DamageType;
use Modules\Battletech\Enums\EquipmentAffiliation;
use Modules\Battletech\Enums\LegalityRating;
use Modules\Battletech\Enums\TechnologyRating;
use Modules\Battletech\Enums\WeaponType;
use Modules\Battletech\Models\Weapon;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('battletech')]
#[Small]
final class WeaponTest extends TestCase
{
    public function testAxe(): void
    {
        $weapon = Weapon::findOrFail('axe');
        self::assertSame('Axe', (string)$weapon);
        self::assertSame(EquipmentAffiliation::General, $weapon->affiliation);
        self::assertSame(3, $weapon->armor_penetration);
        self::assertSame(-2, $weapon->attack_roll);
        self::assertSame(
            [
                AvailabilityRating::VeryCommon,
                AvailabilityRating::VeryCommon,
                AvailabilityRating::VeryCommon,
            ],
            $weapon->availability,
        );
        self::assertSame(2, $weapon->base_damage);
        self::assertSame(25, $weapon->cost);
        self::assertNull($weapon->cost_reload);
        self::assertSame([], $weapon->damage_effects);
        self::assertSame(LegalityRating::Unrestricted, $weapon->legality);
        self::assertSame(4000, $weapon->mass);
        self::assertNull($weapon->mass_reload);
        self::assertSame('-2 to attack roll', $weapon->notes);
        self::assertSame(261, $weapon->page);
        self::assertSame([1], $weapon->range);
        self::assertSame('core', $weapon->ruleset);
        self::assertNull($weapon->shots);
        self::assertSame(TechnologyRating::Primitive, $weapon->tech_level);
        self::assertSame(WeaponType::Melee, $weapon->type);
    }

    public function testAvengerCCW(): void
    {
        $weapon = Weapon::findOrFail('avenger-ccw');
        self::assertSame(EquipmentAffiliation::Clan, $weapon->affiliation);
        self::assertSame(2, $weapon->armor_penetration);
        self::assertNull($weapon->attack_roll);
        self::assertSame(
            [
                AvailabilityRating::NonExistent,
                AvailabilityRating::VeryRare,
                AvailabilityRating::Uncommon,
            ],
            $weapon->availability,
        );
        self::assertSame(6, $weapon->base_damage);
        self::assertSame(345, $weapon->cost);
        self::assertSame(4, $weapon->cost_reload);
        self::assertSame([DamageType::BurstFire, DamageType::Splash], $weapon->damage_effects);
        self::assertSame(LegalityRating::Controlled, $weapon->legality);
        self::assertSame(5500, $weapon->mass);
        self::assertSame(400, $weapon->mass_reload);
        self::assertSame('Avenger CCW', $weapon->name);
        self::assertSame('Burst 3; Recoil -1; Jam on fumble', $weapon->notes);
        self::assertSame(268, $weapon->page);
        self::assertSame([7, 18, 28, 62], $weapon->range);
        self::assertSame('core', $weapon->ruleset);
        self::assertSame(15, $weapon->shots);
        self::assertSame(TechnologyRating::Medium, $weapon->tech_level);
        self::assertSame(WeaponType::Ballistic, $weapon->type);
    }
}
