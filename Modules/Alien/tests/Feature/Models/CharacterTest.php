<?php

declare(strict_types=1);

namespace Modules\Alien\Tests\Feature\Models;

use Modules\Alien\Models\Armor;
use Modules\Alien\Models\Character;
use Modules\Alien\Models\Injury;
use Modules\Alien\Models\Skill;
use Modules\Alien\Models\Talent;
use Modules\Alien\Models\Weapon;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('alien')]
#[Small]
final class CharacterTest extends TestCase
{
    public function testToString(): void
    {
        $character = new Character(['name' => 'Bob']);
        self::assertSame('Bob', (string)$character);
    }

    public function testMaximumHealth(): void
    {
        /** @var Character */
        $character = Character::factory()->make();
        self::assertSame($character->strength, $character->health_maximum);
    }

    public function testMaximumEncumbrance(): void
    {
        /** @var Character */
        $character = Character::factory()->make();
        self::assertSame(
            2 * $character->strength,
            $character->encumbrance_maximum,
        );
    }

    public function testEncumbrance(): void
    {
        $character = new Character();
        self::assertSame(0.0, $character->encumbrance);
        $character->armor = new Armor('m3-personnel-armor');
        self::assertSame(1.0, $character->encumbrance);
        $character->weapons = [
            new Weapon('m4a3-service-pistol'),
        ];
        self::assertSame(1.5, $character->encumbrance);
    }

    public function testSkillsEmpty(): void
    {
        $character = new Character();
        self::assertSame(0, $character->skills['command']->rank);
        self::assertSame(0, $character->skills['mobility']->rank);
    }

    public function testSkillsConstructor(): void
    {
        $character = new Character([
            'skills' => [
                'command' => 3,
            ],
        ]);
        self::assertSame(3, $character->skills['command']->rank);
        self::assertSame(0, $character->skills['mobility']->rank);
    }

    public function testSetSkills(): void
    {
        $character = Character::create();
        $skill = new Skill('mobility', 2);
        $character->skills = [$skill];
        $character->save();

        $character->refresh();
        self::assertSame(2, $character->skills['mobility']->rank);
        $character->delete();
    }

    public function testSkillsModified(): void
    {
        $character = new Character();
        $character->skills = [
            new Skill('close-combat', 1),
            new Skill('heavy-machinery', 2),
            new Skill('survival', 3),
        ];

        self::assertSame(1, $character->skills['close-combat']->rank);
        self::assertSame(2, $character->skills['heavy-machinery']->rank);
        self::assertSame(3, $character->skills['survival']->rank);

        $armor = new Armor('irc-mk-50-compression-suit');
        // None of the armor are this awesome, but this increases a bunch of
        // skills.
        $armor->modifiers = [
            Armor::MODIFIER_CLOSE_COMBAT_INCREASE,
            Armor::MODIFIER_HEAVY_MACHINERY_INCREASE,
            Armor::MODIFIER_SURVIVAL_INCREASE,
        ];
        $character->armor = $armor;

        self::assertSame(4, $character->skills['close-combat']->rank);
        self::assertSame(5, $character->skills['heavy-machinery']->rank);
        self::assertSame(6, $character->skills['survival']->rank);
    }

    public function testTalentsEmpty(): void
    {
        $character = new Character();
        self::assertCount(0, $character->talents);
    }

    public function testTalentsConstructor(): void
    {
        $character = new Character([
            'talents' => [
                'banter',
            ],
        ]);
        self::assertCount(1, $character->talents);
        self::assertSame('Banter', $character->talents[0]->name);
    }

    public function testSetTalents(): void
    {
        $character = new Character();
        $talent = new Talent('bodyguard');
        $character->talents = [$talent];
        $character->save();

        $character->refresh();
        self::assertCount(1, $character->talents);
        self::assertSame('Bodyguard', $character->talents[0]->name);
        $character->delete();
    }

    public function testArmorEmpty(): void
    {
        $character = new Character();
        self::assertNull($character->armor);
    }

    public function testArmorConstructor(): void
    {
        $character = new Character(['armor' => 'm3-personnel-armor']);
        // @phpstan-ignore property.nonObject
        self::assertSame('M3 Personnel Armor', $character->armor->name);
    }

    public function testSetArmor(): void
    {
        $character = new Character();
        $character->armor = new Armor('irc-mk-50-compression-suit');
        self::assertSame('IRC Mk.50 Compression Suit', $character->armor->name);
    }

    public function testModifiedAgilityArmor(): void
    {
        $character = new Character(['agility' => 4]);
        self::assertSame(4, $character->agility);
        $armor = new Armor('irc-mk-50-compression-suit');
        $armor->modifiers = [
            Armor::MODIFIER_AGILITY_DECREASE,
        ];
        $character->armor = $armor;
        self::assertSame(3, $character->agility);
    }

    public function testWeaponsEmpty(): void
    {
        $character = new Character();
        self::assertCount(0, $character->weapons);
    }

    public function testWeaponsConstructor(): void
    {
        $character = new Character([
            'weapons' => [
                'm4a3-service-pistol',
                'spacesub-asso-400-harpoon-grappling-gun',
            ],
        ]);
        self::assertCount(2, $character->weapons);
    }

    public function testSetWeapons(): void
    {
        $character = new Character();
        $character->weapons = [
            new Weapon('m4a3-service-pistol'),
        ];
        self::assertCount(1, $character->weapons);
    }

    public function testInjuriesEmpty(): void
    {
        $character = new Character();
        self::assertCount(0, $character->injuries);
    }

    public function testInjuriesConstructor(): void
    {
        $character = new Character([
            'injuries' => [
                'gouged-eye',
                'broken-leg',
            ],
        ]);
        self::assertCount(2, $character->injuries);
        self::assertSame('Gouged eye', $character->injuries[0]->name);
    }

    public function testSetInjuries(): void
    {
        $character = new Character();
        $injuries = [
            new Injury('crushed-foot'),
        ];
        $character->injuries = $injuries;
        self::assertCount(1, $character->injuries);
        self::assertSame('Crushed foot', $character->injuries[0]->name);
    }

    public function testSkillsWithInjury(): void
    {
        $character = new Character([
            'skills' => [
                'close-combat' => 3,
                'mobility' => 3,
            ],
            'injuries' => [
                // Crippling pain affects stress, not skills.
                'crippling-pain',
                'sprained-ankle',
            ],
        ]);
        // Close combat isn't affected by a sprained ankle (according to the
        // rules).
        self::assertSame(3, $character->skills['close-combat']->rank);
        // A sprined ankle has a big effect on mobility though.
        self::assertSame(1, $character->skills['mobility']->rank);
    }
}
