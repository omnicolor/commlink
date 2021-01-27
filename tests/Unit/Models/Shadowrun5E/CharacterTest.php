<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Shadowrun5E;

use App\Models\Shadowrun5E\Character;

/**
 * Tests for Shadowrun 5E characters.
 * @covers \App\Models\Shadowrun5E\Character
 * @group shadowrun
 * @group shadowrun5e
 * @group models
 */
final class CharacterTest extends \Tests\TestCase
{
    /**
     * Test displaying the character as a string just shows their handle.
     * @test
     */
    public function testToString(): void
    {
        $character = Character::factory()
            ->create(['handle' => 'The Smiling Bandit']);
        self::assertSame('The Smiling Bandit', (string)$character);
        $character->delete();
    }

    /**
     * Test getting the hidden Mongo _id field.
     *
     * It's hidden, but still gettable.
     * @test
     */
    public function testHiddenId(): void
    {
        $character = Character::factory()->create();
        self::assertNotNull($character->_id);
        $character->delete();
    }

    /**
     * Test getting the character's ID.
     * @test
     */
    public function testGetId(): void
    {
        $character = Character::factory()->create();
        self::assertNotNull($character->id);
        self::assertSame($character->_id, $character->id);
        $character->delete();
    }

    /**
     * Test getting a character's qualities if they don't have any.
     * @test
     */
    public function testGetQualitiesEmpty(): void
    {
        $character = new Character();
        self::assertEmpty($character->getQualities());
    }

    /**
     * Test getting a character's qualities if they have one that is invalid.
     * @test
     */
    public function testGetQualitiesInvalid(): void
    {
        $character = new Character(['qualities' => [['id' => 'not-found']]]);
        self::assertEmpty($character->getQualities());
    }
}
