<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Transformers;

use App\Models\Transformers\Character;
use App\Models\Transformers\Mode;
use App\Models\Transformers\Programming;
use App\Models\Transformers\Size;
use App\Models\Transformers\Subgroup;
use App\Models\Transformers\SubgroupArray;
use App\Models\Transformers\Weapon;
use App\Models\Transformers\WeaponArray;
use Tests\TestCase;
use ValueError;

/**
 * Unit tests for Transformers characters.
 * @group models
 * @group transformers
 * @small
 */
final class CharacterTest extends TestCase
{
    public function testToString(): void
    {
        $character = new Character();
        self::assertSame('Unnamed character', (string)$character);
        $character->name = 'Bumblebee';
        self::assertSame('Bumblebee', (string)$character);
    }

    public function testEnergonBase(): void
    {
        $character = new Character([
            'intelligence_robot' => 3,
            'size' => 6,
        ]);
        self::assertSame(8, $character->energon_base);

        $character = new Character([
            'intelligence_robot' => 4,
            'size' => 3,
        ]);
        self::assertSame(14, $character->energon_base);
    }

    public function testEnergonCurrentNoSet(): void
    {
        $character = new Character([
            'intelligence_robot' => 3,
            'size' => 6,
        ]);
        self::assertSame(8, $character->energon_current);
    }

    public function testEnergonCurrentSet(): void
    {
        $character = new Character([
            'energon_current' => 3,
            'intelligence_robot' => 3,
            'size' => 6,
        ]);
        self::assertSame(3, $character->energon_current);
    }

    public function testHpBase(): void
    {
        $character = new Character([
            'endurance_robot' => 3,
            'size' => 6,
        ]);
        self::assertSame(18, $character->hp_base);

        $character = new Character([
            'endurance_robot' => 4,
            'size' => 0,
        ]);
        self::assertSame(10, $character->hp_base);
    }

    public function testHpCurrentNotSet(): void
    {
        $character = new Character([
            'endurance_robot' => 3,
            'size' => 6,
        ]);
        self::assertSame(18, $character->hp_current);
    }

    public function testHpCurrentSet(): void
    {
        $character = new Character([
            'endurance_robot' => 3,
            'hp_current' => 12,
            'size' => 6,
        ]);
        self::assertSame(12, $character->hp_current);
    }

    public function testModeDefault(): void
    {
        $character = new Character();
        self::assertSame(Mode::Robot, $character->mode);
    }

    public function testModeRobot(): void
    {
        $character = new Character(['mode' => 'robot']);
        self::assertSame(Mode::Robot, $character->mode);
    }

    public function testModeAlternate(): void
    {
        $character = new Character();
        $character->mode = Mode::Alternate;
        self::assertEquals(Mode::Alternate, $character->mode);
    }

    public function testSetModeInvalid(): void
    {
        $character = new Character();
        self::expectException(ValueError::class);
        $character->mode = 'foo';
    }

    public function testProgramming(): void
    {
        $character = new Character(['programming' => 'warrior']);
        self::assertSame(Programming::Warrior, $character->programming);
        $character->programming = Programming::Engineer;
        self::assertSame(Programming::Engineer, $character->programming);
        $character->programming = 'scout';
        self::assertSame(Programming::Scout, $character->programming);
    }

    public function testSetProgrammingInvalid(): void
    {
        self::expectException(ValueError::class);
        new Character(['programming' => 'invalid']);
    }

    public function testSizeDefault(): void
    {
        $character = new Character();
        self::assertEquals(Size::Standard, $character->size);
    }

    public function testSizeNonDefault(): void
    {
        $character = new Character(['size' => 8]);
        self::assertEquals(Size::Planet, $character->size);
    }

    public function testSizeSetter(): void
    {
        $character = new Character();
        $character->size = Size::Planet;
        self::assertEquals(Size::Planet, $character->size);
    }

    public function testSizeInvalid(): void
    {
        $character = new Character();
        self::expectException(ValueError::class);
        $character->size = 99;
    }

    public function testGetSubgroups(): void
    {
        $character = new Character(['subgroups' => ['actionmaster']]);
        self::assertCount(1, $character->subgroups);
        self::assertSame('Actionmaster', (string)$character->subgroups[0]);
    }

    public function testSetSubgroupsPlainArray(): void
    {
        $character = new Character();
        $character->subgroups = ['actionmaster'];
        self::assertCount(1, $character->subgroups);
        self::assertSame('Actionmaster', (string)$character->subgroups[0]);
    }

    public function testSetSubgroupsSubgroupArray(): void
    {
        $groups = new SubgroupArray();
        $groups[] = new Subgroup('actionmaster');

        $character = new Character();
        $character->subgroups = $groups;
        self::assertCount(1, $character->subgroups);
        self::assertSame('Actionmaster', (string)$character->subgroups[0]);
    }

    public function testInvalidSubgroup(): void
    {
        $character = new Character(['subgroups' => ['invalid']]);
        self::assertCount(0, $character->subgroups);
    }

    public function testGetWeaponsEmpty(): void
    {
        $character = new Character();
        self::assertCount(0, $character->weapons);
    }

    public function testGetWeapons(): void
    {
        $character = new Character(['weapons' => ['buzzsaw']]);
        self::assertCount(1, $character->weapons);
        self::assertSame('Buzzsaw', (string)$character->weapons[0]);
    }

    public function testSetWeaponsPlainArray(): void
    {
        $character = new Character();
        self::assertCount(0, $character->weapons);
        $character->weapons = ['buzzsaw'];
        self::assertCount(1, $character->weapons);
    }

    public function testSetWeaponsWeaponArray(): void
    {
        $weapons = new WeaponArray();
        $weapons[] = new Weapon('buzzsaw');

        $character = new Character();
        $character->weapons = $weapons;
        self::assertCount(1, $character->weapons);
    }

    public function testInvalidWeapon(): void
    {
        $character = new Character(['weapons' => ['invalid']]);
        self::assertCount(0, $character->weapons);
    }
}
