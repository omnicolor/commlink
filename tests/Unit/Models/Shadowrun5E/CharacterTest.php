<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Shadowrun5E;

use App\Models\Shadowrun5E\Character;

/**
 * Tests for Shadowrun 5E characters.
 * @covers \App\Models\Shadowrun5E\Character
 * @group models
 * @group shadowrun
 * @group shadowrun5e
 */
final class CharacterTest extends \Tests\TestCase
{
    /**
     * Character to test on.
     * @var ?Character
     */
    protected ?Character $character;

    /**
     * Clean up after the tests.
     */
    public function tearDown(): void
    {
        if (isset($this->character)) {
            $this->character->delete();
            unset($this->character);
        }
        parent::tearDown();
    }

    /**
     * Test displaying the character as a string just shows their handle.
     * @test
     */
    public function testToString(): void
    {
        $this->character = Character::factory()
            ->create(['handle' => 'The Smiling Bandit']);
        self::assertSame('The Smiling Bandit', (string)$this->character);
    }

    /**
     * Test getting the hidden Mongo _id field.
     *
     * It's hidden, but still gettable.
     * @test
     */
    public function testHiddenId(): void
    {
        $this->character = Character::factory()->create();
        self::assertNotNull($this->character->_id);
    }

    /**
     * Test getting the character's ID.
     * @test
     */
    public function testGetId(): void
    {
        $this->character = Character::factory()->create();
        self::assertNotNull($this->character->id);
        self::assertSame($this->character->_id, $this->character->id);
    }

    /**
     * Test getting a character's adept powers if they don't have any.
     * @test
     */
    public function testGetAdeptPowersEmpty(): void
    {
        $character = new Character();
        self::assertEmpty($character->getAdeptPowers());
    }

    /**
     * Test getting a character's adept powers if they have an invalid power.
     * @test
     */
    public function testGetAdeptPowersInvalid(): void
    {
        $character = new Character([
            'magics' => [
                'powers' => [
                    'not-found',
                ],
            ],
        ]);
        self::assertEmpty($character->getAdeptPowers());
    }

    /**
     * Test getting a character's adept powers.
     * @test
     */
    public function testGetAdeptPowers(): void
    {
        $character = new Character([
            'magics' => [
                'powers' => [
                    'improved-sense-direction-sense',
                ],
            ],
        ]);
        self::assertCount(1, $character->getAdeptPowers());
    }

    /**
     * Test getting a character's armor if they don't have any.
     * @test
     */
    public function testGetArmorEmpty(): void
    {
        $character = new Character();
        self::assertEmpty($character->getArmor());
    }

    /**
     * Test getting a character's armor if they have one that is invalid.
     * @test
     */
    public function testGetArmorInvalid(): void
    {
        $character = new Character(['armor' => [['id' => 'not-found']]]);
        self::assertEmpty($character->getArmor());
    }

    /**
     * Test getting a character's armor.
     * @test
     */
    public function testGetArmor(): void
    {
        $character = new Character([
            'armor' => [
                ['id' => 'armor-jacket'],
                ['id' => 'berwick-suit'],
            ],
        ]);
        self::assertCount(2, $character->getArmor());
    }

    /**
     * Test getting a character's augmentations if they don't have any.
     * @test
     */
    public function testGetAugmentationsEmpty(): void
    {
        $character = new Character();
        self::assertEmpty($character->getAugmentations());
    }

    /**
     * Test getting a character's augmentations if they have an invalid one.
     * @test
     */
    public function testGetAugmentationsInvalid(): void
    {
        $character = new Character([
            'augmentations' => [['id' => 'not-found']],
        ]);
        self::assertEmpty($character->getAugmentations());
    }

    /**
     * Test getting a character's augmentations.
     * @test
     */
    public function testGetAugmentations(): void
    {
        $character = new Character([
            'augmentations' => [
                ['id' => 'cyberears-1'],
                ['id' => 'cybereyes-1'],
            ],
        ]);
        self::assertCount(2, $character->getAugmentations());
    }

    /**
     * Test getting the metatype of a character with no priorities.
     * @test
     */
    public function testGetMetatypeNoPriority(): void
    {
        $character = new Character();
        self::assertSame('unknown', $character->metatype);
    }

    /**
     * Test getting the metatype of a character with incomplete priorities.
     * @test
     */
    public function testGetMetatypeIncompletePriorities(): void
    {
        $character = new Character(['priorities' => []]);
        self::assertSame('unknown', $character->metatype);
    }

    /**
     * Test getting the metatype of a character with standard priorities.
     * @test
     */
    public function testGetMetatypeStandardPriorities(): void
    {
        $character = new Character([
            'priorities' => [
                'a' => 'attributes',
                'b' => 'skills',
                'c' => 'resources',
                'd' => 'metatype',
                'e' => 'magic',
                'metatype' => 'elf',
                'magic' => '',
                'gameplay' => 'established',
            ],
        ]);
        self::assertSame('elf', $character->metatype);
    }

    /**
     * Test getting the metatype of a character with sum-to-ten priorities.
     * @test
     */
    public function testGetMetatypeSumToTenPriorities(): void
    {
        $character = new Character([
            'priorities' => [
                'metatype' => 'human',
                'metatypePriority' => 'E',
                'magicPriority' => 'B',
                'attributePriority' => 'A',
                'skillPriority' => 'B',
                'resourcePriority' => 'E',
                'magic' => 'technomancer',
                'gameplay' => 'established',
            ],
        ]);
        self::assertSame('human', $character->metatype);
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

    /**
     * Test getting a character's qualities.
     * @test
     */
    public function testGetQualities(): void
    {
        $character = new Character([
            'qualities' => [
                ['id' => 'lucky'],
                ['id' => 'addiction-mild', 'addiction' => 'Alcohol']
            ]
        ]);
        self::assertCount(2, $character->getQualities());
    }

    /**
     * Test getting a character's skills if they have none.
     * @test
     */
    public function testGetSkillsEmpty(): void
    {
        $character = new Character();
        self::assertEmpty($character->getSkills());
    }

    /**
     * Test getting a character's skills if they have one that is invalid.
     * @test
     */
    public function testGetSkillsInvalid(): void
    {
        $character = new Character([
            'skills' => [
                ['id' => 'not-found', 'level' => 6],
            ]
        ]);
        self::assertEmpty($character->getSkills());
    }

    /**
     * Test getting a character's skills.
     * @test
     */
    public function testGetSkills(): void
    {
        $character = new Character([
            'skills' => [
                ['id' => 'automatics', 'level' => 6],
                ['id' => 'hacking', 'level' => 5, 'specialization' => 'foo'],
            ],
        ]);
        self::assertCount(2, $character->getSkills());
    }
}
