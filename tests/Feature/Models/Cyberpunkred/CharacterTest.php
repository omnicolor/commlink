<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Cyberpunkred;

use App\Models\Cyberpunkred\Character;
use App\Models\Cyberpunkred\Role\Fixer;
use App\Models\Cyberpunkred\Weapon;
use RuntimeException;
use Tests\TestCase;

/**
 * Unit tests for Cyberpunkred Characters.
 * @group cyberpunkred
 * @group models
 * @small
 */
final class CharacterTest extends TestCase
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
            'hit_points_current' => 100,
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
        self::assertSame(100, $character->hit_points_current);
        self::assertSame(40, $character->hit_points_max);
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
     * Test getting the character's death save.
     * @test
     */
    public function testGetDeathSave(): void
    {
        $body = random_int(1, 15);
        $character = new Character(['body' => $body]);
        self::assertSame($body, $character->death_save);
    }

    /**
     * Return different datasets for testing hit points.
     * @return array<int, array<int, int>>
     */
    public static function hitPointsProvider(): array
    {
        return [
            [0, 0, 10],
            [1, 0, 15],
            [0, 1, 15],
            [0, 10, 35],
            [10, 10, 60],
        ];
    }

    /**
     * Data provider for the max hit points tests.
     * @dataProvider hitPointsProvider
     * @param int $body
     * @param int $will
     * @param int $hp
     */
    public function testHitPointsMax(int $body, int $will, int $hp): void
    {
        $character = new Character(['body' => $body, 'willpower' => $will]);
        self::assertSame($hp, $character->hit_points_max);
    }

    /**
     * Return different data sets for calculating the character's humanity.
     * @return array<int, array<int, int>>
     */
    public static function humanityProvider(): array
    {
        return [
            [0, 0],
            [1, 10],
            [2, 20],
            [10, 100],
        ];
    }

    /**
     * Test getting the character's humanity.
     * @dataProvider humanityProvider
     * @param int $empathy
     * @param int $humanity
     */
    public function testHumanity(int $empathy, int $humanity): void
    {
        $character = new Character(['empathy' => $empathy]);
        self::assertSame($humanity, $character->humanity);
    }

    /**
     * Return different datasets for testing getting the threshold.
     * @return array<int, array<int, int>>
     */
    public static function woundThresholdProvider(): array
    {
        return [
            [0, 0, 5],
            [10, 10, 30],
            [15, 10, 38],
        ];
    }

    /**
     * Test getting the serious wound threshold.
     * @dataProvider woundThresholdProvider
     * @param int $body
     * @param int $will
     * @param int $threshold
     * @test
     */
    public function testSeriousWoundThreshold(
        int $body,
        int $will,
        int $threshold,
    ): void {
        $character = new Character(['body' => $body, 'willpower' => $will]);
        self::assertSame($threshold, $character->seriously_wounded_threshold);
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
            if ('concentration' === $skill->id) {
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
            if ('business' !== $skill->id) {
                continue;
            }
            self::assertSame(4, $skill->level);
            return;
        }
        self::fail('Skill not found');
    }

    /**
     * Test getting the character's original empathy statistic.
     * @test
     */
    public function testGetOriginalEmpathy(): void
    {
        $character = new Character(['empathy' => 5]);
        self::assertSame(5, $character->empathy_original);
    }

    /**
     * Test trying to get an invalid class of weapons.
     * @test
     */
    public function testGetInvalidWeaponsType(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Invalid Weapon Type');
        (new Character())->getWeapons('unknown');
    }

    /**
     * Test trying to get the different classes of weapons.
     * @test
     */
    public function testGetWeapons(): void
    {
        $character = new Character([
            'weapons' => [
                ['id' => 'medium-pistol'],
                ['id' => 'medium-melee'],
                ['id' => 'medium-melee'],
                ['id' => 'invalid'],
            ],
        ]);
        self::assertCount(3, $character->getWeapons());
        self::assertCount(2, $character->getWeapons(Weapon::TYPE_MELEE));
        self::assertCount(1, $character->getWeapons(Weapon::TYPE_RANGED));
    }
}
