<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Dnd5e;

use App\Models\Character as BaseCharacter;
use App\Models\Dnd5e\Character;
use OutOfRangeException;
use RuntimeException;
use Tests\TestCase;

use function is_subclass_of;

/**
 * Tests for D&D 5E characters.
 * @group models
 * @group dnd5e
 * @small
 */
final class CharacterTest extends TestCase
{
    /**
     * Test displaying the character as a string.
     * @test
     */
    public function testToString(): void
    {
        $character = new Character(['name' => 'Conan the Agrarian']);
        self::assertSame('Conan the Agrarian', (string)$character);
    }

    /**
     * Test that the character's system is set correctly.
     * @test
     */
    public function testSystem(): void
    {
        $character = new Character();
        self::assertSame('dnd5e', $character->system);
    }

    /**
     * Test loading a D&D 5E character.
     * @test
     */
    public function testLoad(): void
    {
        $createdCharacter = BaseCharacter::create(['system' => 'dnd5e']);
        $character = BaseCharacter::where('_id', $createdCharacter->id)
            ->firstOrFail();
        self::assertFalse(is_subclass_of($character, Character::class));
    }

    /**
     * Test getting the character's ability modifier for an invalid attribute.
     * @test
     */
    public function testGetAbilityModifierInvalid(): void
    {
        $character = new Character();
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Invalid attribute');
        $character->getAbilityModifier('invalid');
    }

    /**
     * Test getting the character's ability modifier if the value is out of
     * acceptable range.
     * @test
     */
    public function testGetAbilityModifierOutOfRange(): void
    {
        $character = new Character(['charisma' => 0]);
        self::expectException(OutOfRangeException::class);
        self::expectExceptionMessage('Attribute value is out of range');
        $character->getAbilityModifier('charisma');
    }

    /**
     * Test getting the character's ability modifier for a few different
     * values.
     * @test
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
        self::assertSame(-3, $character->getAbilityModifier('charisma'));
        self::assertSame(0, $character->getAbilityModifier('constitution'));
        self::assertSame(2, $character->getAbilityModifier('dexterity'));
        self::assertSame(5, $character->getAbilityModifier('intelligence'));
        self::assertSame(7, $character->getAbilityModifier('strength'));
        self::assertSame(10, $character->getAbilityModifier('wisdom'));
    }

    /**
     * Test getting the character's armor class if the dexterity is not set.
     * @test
     */
    public function testGetArmorClassNotSet(): void
    {
        $character = new Character();
        self::expectException(RuntimeException::class);
        $character->getArmorClass();
    }

    /**
     * Test getting the character's armor class if the dexterity is out of
     * range.
     * @test
     */
    public function testGetArmorClassOutOfRange(): void
    {
        $character = new Character(['dexterity' => 99]);
        self::expectException(OutOfRangeException::class);
        $character->getArmorClass();
    }

    /**
     * Test getting the character's armor class.
     * @test
     */
    public function testGetArmorClass(): void
    {
        $character = new Character(['dexterity' => random_int(1, 30)]);
        self::assertSame(
            (int)floor($character->dexterity / 2) - 5 + 10,
            $character->getArmorClass()
        );
    }
}
