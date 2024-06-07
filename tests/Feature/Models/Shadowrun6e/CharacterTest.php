<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun6e;

use App\Models\Shadowrun6e\Character;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

/**
 * Tests for Shadowrun 6E characters.
 * @group shadowrun
 * @group shadowrun6e
 */
#[Small]
final class CharacterTest extends TestCase
{
    use WithFaker;

    /**
     * Test toString if the character has neither a handle nor a name.
     */
    public function testToStringNoHandleOrName(): void
    {
        $character = new Character();
        self::assertSame('Unnamed Character', (string)$character);
    }

    /**
     * Test toString if the character has a name but no handle.
     */
    public function testToStringNoHandle(): void
    {
        $character = new Character(['name' => 'Phil']);
        self::assertSame('Phil', (string)$character);
    }

    /**
     * Test toString if the character has a handle and a name.
     */
    public function testToString(): void
    {
        $character = new Character([
            'handle' => 'The Smiling Bandit',
            'name' => 'Ha! Ha! Ha!',
        ]);
        self::assertSame('The Smiling Bandit', (string)$character);
    }

    /**
     * Test getting the character's qualities if they have none.
     */
    public function testGetQualitiesNone(): void
    {
        $character = new Character();
        self::assertEmpty($character->getQualities());
    }

    /**
     * Test getting the character's qualities if they only have an invalid one.
     */
    public function testGetQualitiesInvalid(): void
    {
        $character = new Character([
            'qualities' => [
                ['id' => 'not-found'],
            ],
        ]);
        self::assertEmpty($character->getQualities());
    }

    /**
     * Test getting the character's qualities.
     */
    public function testGetQualities(): void
    {
        $character = new Character([
            'qualities' => [
                ['id' => 'ambidextrous'],
                ['id' => 'focused-concentration-1'],
            ],
        ]);
        self::assertCount(2, $character->getQualities());
    }

    /**
     * Test getting the character's initiative.
     */
    public function testInitiative(): void
    {
        $intuition = $this->faker->randomDigit();
        $reaction = $this->faker->randomDigit();
        $character = new Character([
            'intuition' => $intuition,
            'reaction' => $reaction,
        ]);
        self::assertSame($intuition + $reaction, $character->initiative_base);
        self::assertSame(1, $character->initiative_dice);
    }
}
