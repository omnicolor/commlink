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

    /**
     * Test getting a character's skills if the have none.
     * @test
     */
    public function testGetSkillsNone(): void
    {
        $character = new Character();
        self::assertEmpty($character->getSkills());
    }

    /**
     * Test getting a character's skills if they only have an invalid one.
     * @test
     */
    public function testGetSkillsInvalid(): void
    {
        $character = new Character(['skills' => ['invalid' => 1]]);
        self::assertEmpty($character->getSkills());
    }

    /**
     * Test getting a character's skills if they have a valid skill.
     * @test
     */
    public function testGetSkills(): void
    {
        $character = new Character(['skills' => ['business' => 1]]);
        self::assertNotEmpty($character->getSkills());
        // @phpstan-ignore-next-line
        self::assertSame(1, $character->getSkills()[0]->level);
    }

    /**
     * Test getting all skills if the character doesn't have any levels.
     * @test
     */
    public function testGetAllSkillsNoRanks(): void
    {
        $character = new Character();
        self::assertNotEmpty($character->getAllSkills());
        foreach ($character->getAllSkills() as $skill) {
            self::assertSame(0, $skill->level);
        }
    }

    /**
     * Test getting all skills if the character has levels.
     * @test
     */
    public function testGetAllSkills(): void
    {
        $character = new Character(['skills' => ['concentration' => 2]]);
        $skills = $character->getAllSkills();
        $concentrationSeen = false;
        foreach ($skills as $skill) {
            if ($skill->id === 'concentration') {
                self::assertSame(2, $skill->level);
                $concentrationSeen = true;
                continue;
            }
            self::assertSame(0, $skill->level);
        }
        self::assertTrue($concentrationSeen);
    }

    /**
     * Test getting all skills categorized if the character doesn't have any
     * levels.
     * @test
     */
    public function testGetSkillsByCategoryNoRanks(): void
    {
        $character = new Character();
        $skills = $character->getSkillsByCategory();
        self::assertArrayHasKey('Awareness', $skills);
        self::assertArrayHasKey('Education', $skills);
        // @phpstan-ignore-next-line
        self::assertSame(0, $skills['Awareness'][0]->level);
        // @phpstan-ignore-next-line
        self::assertSame(0, $skills['Education'][0]->level);
    }

    /**
     * Test getting all skills categorized if the character has some levels.
     * @test
     */
    public function testGetSkillsByCategory(): void
    {
        $character = new Character(['skills' => ['business' => 4]]);
        $skills = $character->getSkillsByCategory();
        self::assertArrayHasKey('Education', $skills);
        foreach ($skills['Education'] as $skill) {
            if ($skill->id !== 'business') {
                continue;
            }
            self::assertSame(4, $skill->level);
            return;
        }
        self::fail('Skill not found');
    }
}
