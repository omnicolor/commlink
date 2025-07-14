<?php

declare(strict_types=1);

namespace Modules\Dnd5e\Tests\Feature\Models;

use App\Models\Character as BaseCharacter;
use Modules\Dnd5e\Models\Character;
use Modules\Dnd5e\ValueObjects\AbilityValue;
use Modules\Dnd5e\ValueObjects\CharacterLevel;
use OutOfRangeException;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;
use TypeError;

use function is_subclass_of;

#[Group('dnd5e')]
#[Small]
final class CharacterTest extends TestCase
{
    public function testToString(): void
    {
        $character = new Character(['name' => 'Conan the Agrarian']);
        self::assertSame('Conan the Agrarian', (string)$character);
    }

    public function testSystem(): void
    {
        $character = new Character();
        self::assertSame('dnd5e', $character->system);
    }

    public function testLoad(): void
    {
        /** @var BaseCharacter $createdCharacter */
        $createdCharacter = BaseCharacter::create(['system' => 'dnd5e']);
        $character = BaseCharacter::where('_id', $createdCharacter->id)
            ->firstOrFail();
        self::assertFalse(is_subclass_of($character, Character::class));
    }

    /**
     * Test getting the character's ability modifier if the value is out of
     * acceptable range.
     */
    public function testGetAbilityModifierOutOfRange(): void
    {
        $character = new Character(['charisma' => 0]);
        self::expectException(OutOfRangeException::class);
        self::expectExceptionMessage('Attribute value is out of range');
        // @phpstan-ignore expr.resultUnused
        $character->charisma;
    }

    /**
     * Test getting the character's ability modifier for a few different
     * values.
     */
    public function testGetAbilityModifier(): void
    {
        $character = new Character([
            'charisma' => 5,
            'constitution' => 10,
            'dexterity' => 15,
            'intelligence' => 20,
            'strength' => 25,
            'wisdom' => 30,
        ]);
        self::assertSame(-3, $character->charisma->modifier);
        self::assertSame(0, $character->constitution->modifier);
        self::assertSame(2, $character->dexterity->modifier);
        self::assertSame(5, $character->intelligence->modifier);
        self::assertSame(7, $character->strength->modifier);
        self::assertSame(10, $character->wisdom->modifier);
    }

    /**
     * Test getting the character's armor class if the dexterity is not set.
     */
    public function testGetArmorClassNotSet(): void
    {
        $character = new Character();
        self::expectException(TypeError::class);
        // @phpstan-ignore expr.resultUnused
        $character->armor_class;
    }

    /**
     * Test getting the character's armor class if the dexterity is out of
     * range.
     */
    public function testGetArmorClassOutOfRange(): void
    {
        $character = new Character(['dexterity' => 99]);
        self::expectException(OutOfRangeException::class);
        // @phpstan-ignore expr.resultUnused
        $character->armor_class;
    }

    /**
     * Test getting the character's armor class.
     */
    public function testGetArmorClass(): void
    {
        $dexterity = random_int(1, 30);
        $character = new Character(['dexterity' => $dexterity]);
        self::assertSame(
            (int)floor($dexterity / 2) - 5 + 10,
            $character->armor_class,
        );
    }

    public function testGetAttributes(): void
    {
        $character = new Character([
            'strength' => 17,
            'dexterity' => 10,
            'constitution' => 16,
            'intelligence' => 8,
            'wisdom' => 13,
            'charisma' => 12,
        ]);
        self::assertEquals(new AbilityValue(17), $character->strength);
        self::assertEquals(new AbilityValue(10), $character->dexterity);
        self::assertEquals(new AbilityValue(16), $character->constitution);
        self::assertEquals(new AbilityValue(8), $character->intelligence);
        self::assertEquals(new AbilityValue(13), $character->wisdom);
        self::assertEquals(new AbilityValue(12), $character->charisma);
    }

    public function testGetLevel(): void
    {
        $character = new Character([]);
        self::assertEquals('1', (string)$character->level);

        $character = new Character(['experience_points' => 1_000_000]);
        self::assertEquals('20', (string)$character->level);
    }
}
