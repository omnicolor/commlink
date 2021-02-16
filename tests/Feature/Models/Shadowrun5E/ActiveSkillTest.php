<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5E;

use App\Models\Shadowrun5E\ActiveSkill;

/**
 * Unit tests for ActiveSkill class.
 * @covers \App\Models\Shadowrun5E\ActiveSkill
 * @covers \App\Models\Shadowrun5E\Skill
 * @group models
 * @group shadowrun
 * @group shadowrun5e
 */
final class ActiveSkillTest extends \Tests\TestCase
{
    /**
     * Test trying to load an invalid skill throws an exception.
     * @test
     */
    public function testLoadingInvalidSkill(): void
    {
        ActiveSkill::$skills = null;
        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('Skill ID "not-found-id" is invalid');
        new ActiveSkill('not-found-id', 0);
    }

    /**
     * Test loading a skill.
     * Active@return Skill
     * @test
     */
    public function testLoadSkillSetsId(): ActiveSkill
    {
        $skill = new ActiveSkill('automatics', 4);
        self::assertEquals('automatics', $skill->id);
        return $skill;
    }

    /**
     * Test that loading a skill sets the linked attribute.
     * @depends testLoadSkillSetsId
     * @param ActiveSkill $skill
     * @test
     */
    public function testLoadSkillSetsAttribute(ActiveSkill $skill): void
    {
        self::assertEquals('agility', $skill->attribute);
    }

    /**
     * Test that loading a skill sets default property if the skill can be
     * defaulted to.
     * @depends testLoadSkillSetsId
     * @param ActiveSkill $skill
     * @test
     */
    public function testLoadSkillSetsDefault(ActiveSkill $skill): void
    {
        self::assertTrue($skill->default);
    }

    /**
     * Test that loading a skill sets the description.
     * @depends testLoadSkillSetsId
     * @param ActiveSkill $skill
     * @test
     */
    public function testLoadSkillSetsDescription(ActiveSkill $skill): void
    {
        $expected = 'Skill description here.';
        self::assertEquals($expected, $skill->description);
    }

    /**
     * Test that loading a skills sets the group.
     * @depends testLoadSkillSetsId
     * @param ActiveSkill $skill
     * @test
     */
    public function testLoadSkillSetsGroup(ActiveSkill $skill): void
    {
        self::assertEquals('firearms', $skill->group);
    }

    /**
     * Test that loading a skill sets the level.
     * @depends testLoadSkillSetsId
     * @param ActiveSkill $skill
     * @test
     */
    public function testLoadSkillSetsLevel(ActiveSkill $skill): void
    {
        self::assertEquals(4, $skill->level);
    }

    /**
     * Test that loading a skill sets the name.
     * @depends testLoadSkillSetsId
     * @param ActiveSkill $skill
     * @test
     */
    public function testLoadSkillSetsName(ActiveSkill $skill): void
    {
        self::assertEquals('Automatics', $skill->name);
    }

    /**
     * Test the __toString method.
     * @depends testLoadSkillSetsId
     * @param ActiveSkill $skill
     * @test
     */
    public function testLoadSkillToString(ActiveSkill $skill): void
    {
        self::assertEquals('Automatics', (string)$skill);
    }

    /**
     * Test that loading a skill without a group doesn't change the group
     * property.
     * @test
     */
    public function testLoadGrouplessSkill(): void
    {
        $skill = new ActiveSkill('astral-combat', 3);
        self::assertNull($skill->group);
    }

    /**
     * Test that loading a skill that can't be defaulted doesn't change the
     * default property.
     * @test
     */
    public function testLoadNotDefaultableSkill(): void
    {
        $skill = new ActiveSkill('astral-combat', 3);
        self::assertFalse($skill->default);
    }

    /**
     * Test trying to find the ID of a skill if the skill isn't found.
     * @test
     */
    public function testFindIdByNameNotFound(): void
    {
        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('Active skill "Foo" not found');
        ActiveSkill::$skills = null;
        ActiveSkill::findIdByName('Foo');
    }

    /**
     * Test finding a skill's ID by its name.
     * @test
     */
    public function testFindIdByName(): void
    {
        self::assertSame(
            'automatics',
            ActiveSkill::findIdByName('Automatics')
        );
        self::assertSame(
            'astral-combat',
            ActiveSkill::findIdByName('Astral Combat')
        );
    }
}
