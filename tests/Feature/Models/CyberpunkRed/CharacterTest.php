<?php

declare(strict_types=1);

namespace Tests\Feature\Models\CyberpunkRed;

use App\Models\CyberpunkRed\Character;
use App\Models\CyberpunkRed\Role\Fixer;

/**
 * Unit tests for CyberpunkRed Characters.
 * @covers \App\Models\CyberpunkRed\Character
 * @group cyberpunkred
 * @group models
 */
final class CharacterTest extends \Tests\TestCase
{
    /**
     * Test filling up a character with the constructor.
     * @test
     */
    public function testConstructor(): void
    {
        $character = new Character([
            'body' => 1,
            'cool' => 2,
            'dexterity' => 3,
            'empathy' => 4,
            'handle' => 'Test Character',
            'hitPointsCurrent' => 100,
            'hitPointsMax' => 200,
            'intelligence' => 5,
            'luck' => 6,
            'movement' => 7,
            'reflexes' => 8,
            'technique' => 9,
            'willpower' => 10,
        ]);
        self::assertSame(1, $character->body);
        self::assertSame(2, $character->cool);
        self::assertSame(3, $character->dexterity);
        self::assertSame(4, $character->empathy);
        self::assertSame('Test Character', $character->handle);
        self::assertSame(100, $character->hitPointsCurrent);
        self::assertSame(200, $character->hitPointsMax);
        self::assertSame(5, $character->intelligence);
        self::assertSame(6, $character->luck);
        self::assertSame(7, $character->movement);
        self::assertSame(8, $character->reflexes);
        self::assertSame(9, $character->technique);
        self::assertSame(10, $character->willpower);
    }

    /**
     * Test the __toString() method.
     * @test
     */
    public function testToString(): void
    {
        $character = new Character(['handle' => 'Bob King']);
        self::assertSame('Bob King', (string)$character);
    }

    /**
     * Test getting a character's roles if they have none.
     * @test
     */
    public function testGetRolesNone(): void
    {
        $character = new Character();
        self::assertEmpty($character->getRoles());
    }

    /**
     * Test getting a character's roles if they only have an invalid one.
     * @test
     */
    public function testGetRolesInvalid(): void
    {
        $character = new Character(['roles' => [['role' => 'invalid']]]);
        self::assertEmpty($character->getRoles());
    }

    /**
     * Test getting a character's roles.
     * @test
     */
    public function testGetRoles(): void
    {
        $character = new Character([
            'roles' => [
                [
                    'role' => 'fixer',
                    'rank' => 4,
                    'type' => Fixer::TYPE_BROKER_DEALS,
                ],
            ],
        ]);
        self::assertNotEmpty($character->getRoles());
        self::assertInstanceOf(Fixer::class, $character->getRoles()[0]);
    }
}
