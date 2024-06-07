<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5e;

use App\Models\Shadowrun5e\ActiveSkill;
use App\Models\Shadowrun5e\SkillGroup;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

/**
 * Unit tests for SkillGroups.
 * @group shadowrun
 * @group shadowrun5e
 */
#[Small]
final class SkillGroupTest extends TestCase
{
    /**
     * @var SkillGroup Subject under test
     */
    protected SkillGroup $skillGroup;

    /**
     * Set up a clean subject under test.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->skillGroup = new SkillGroup('firearms', 3);
    }

    /**
     * Test loading an invalid skill group.
     */
    public function testInvalidGroup(): void
    {
        SkillGroup::$skillGroups = null;
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage(
            'Skill group ID "invalid-group-id" is invalid'
        );
        new SkillGroup('invalid-group-id', 15);
    }

    /**
     * Test that loading the skill group sets the ID.
     */
    public function testSetsId(): void
    {
        self::assertEquals('firearms', $this->skillGroup->id);
    }

    /**
     * Test that loading the skill group sets the level.
     */
    public function testSetsLevel(): void
    {
        self::assertEquals(3, $this->skillGroup->level);
    }

    /**
     * Test that loading the skill group sets the name.
     */
    public function testSetsName(): void
    {
        self::assertEquals('Firearms', $this->skillGroup->name);
    }

    /**
     * Test that loading the skill group sets the skills that are part of the
     * group.
     */
    public function testSetsSubSkills(): void
    {
        foreach ($this->skillGroup->skills as $skill) {
            self::assertInstanceOf(ActiveSkill::class, $skill);
            self::assertEquals(0, $skill->level);
        }
    }

    /**
     * Test casting the group to a string.
     */
    public function testToString(): void
    {
        self::assertEquals('Firearms', (string)$this->skillGroup);
    }
}
