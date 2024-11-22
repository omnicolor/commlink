<?php

declare(strict_types=1);

namespace Modules\StartrekAdventures\Tests\Feature\Models;

use Modules\Startrekadventures\Models\Character;
use Modules\Startrekadventures\Models\Species;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('startrekadventures')]
#[Small]
final class CharacterTest extends TestCase
{
    /**
     * Test loading from data store.
     */
    public function testNewFromBuilder(): void
    {
        $character = new Character(['name' => 'Test STA character']);
        $character->save();

        $loaded = Character::find($character->id);
        self::assertInstanceOf(Character::class, $loaded);
        self::assertSame('Test STA character', $loaded->name);
        $character->delete();
    }

    /**
     * Test converting a character without a name to a string.
     */
    public function testToStringNoName(): void
    {
        self::assertSame('Unnamed Character', (string)(new Character()));
    }

    /**
     * Test toString on a character with a name.
     */
    public function testToString(): void
    {
        self::assertSame('Spock', (string)(new Character(['name' => 'Spock'])));
    }

    /**
     * Test getting a character's attributes.
     */
    public function testGetAttributes(): void
    {
        $character = new Character([
            'attributes' => [
                'control' => 3,
                'daring' => 4,
                'fitness' => 5,
                'insight' => 6,
                'presence' => 7,
                'reason' => 8,
            ],
        ]);
        self::assertSame(3, $character->stats->control);
        self::assertSame(4, $character->stats->daring);
        self::assertSame(5, $character->stats->fitness);
        self::assertSame(6, $character->stats->insight);
        self::assertSame(7, $character->stats->presence);
        self::assertSame(8, $character->stats->reason);
    }

    /**
     * Test getting a character's disciplines.
     */
    public function testGetDisciplines(): void
    {
        $character = new Character([
            'disciplines' => [
                'command' => 3,
                'conn' => 4,
                'security' => 5,
                'engineering' => 6,
                'science' => 7,
                'medicine' => 8,
            ],
        ]);
        self::assertSame(3, $character->disciplines->command);
        self::assertSame(4, $character->disciplines->conn);
        self::assertSame(5, $character->disciplines->security);
        self::assertSame(6, $character->disciplines->engineering);
        self::assertSame(7, $character->disciplines->science);
        self::assertSame(8, $character->disciplines->medicine);
    }

    public function testGetFocusesNotSet(): void
    {
        $character = new Character();
        self::assertSame([], $character->focuses);
    }

    public function testGetFocusesNotArray(): void
    {
        $character = new Character(['focuses' => 'test']);
        self::assertSame([], $character->focuses);
    }

    public function testGetFocuses(): void
    {
        $character = new Character(['focuses' => ['Virology']]);
        self::assertSame(['Virology'], $character->focuses);
    }

    /**
     * Test getting the character's species.
     */
    public function testSpecies(): void
    {
        $character = new Character(['species' => 'Human']);
        self::assertInstanceOf(Species::class, $character->species);
        self::assertSame('Human', (string)$character->species);
    }

    /**
     * Test getting the character's stress attribute.
     */
    public function testGetStress(): void
    {
        $character = new Character([
            'attributes' => [
                'control' => 3,
                'daring' => 4,
                'fitness' => 5,
                'insight' => 6,
                'presence' => 7,
                'reason' => 8,
            ],
            'disciplines' => [
                'command' => 3,
                'conn' => 4,
                'security' => 5,
                'engineering' => 6,
                'science' => 7,
                'medicine' => 8,
            ],
        ]);
        self::assertSame(10, $character->stress);
    }

    /**
     * Test getting a character's talents if they have none.
     */
    public function testGetTalentsNone(): void
    {
        $character = new Character();
        self::assertEmpty($character->talents);
    }

    /**
     * Test getting a character's talents.
     */
    public function testGetTalents(): void
    {
        $character = new Character([
            'talents' => [
                ['id' => 'bold-command'],
            ],
        ]);
        self::assertCount(1, $character->talents);
    }

    /**
     * Test trying to get a character's talents if they've got an invalid one.
     */
    public function testGetTalentsInvalid(): void
    {
        $character = new Character([
            'talents' => [
                ['id' => 'invalid'],
            ],
        ]);
        self::assertEmpty($character->talents);
    }
}
