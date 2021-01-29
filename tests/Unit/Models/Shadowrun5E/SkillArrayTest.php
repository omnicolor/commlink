<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Shadowrun5E;

use App\Models\Shadowrun5E\ActiveSkill;
use App\Models\Shadowrun5E\AdeptPower;
use App\Models\Shadowrun5E\Skill;
use App\Models\Shadowrun5E\SkillArray;

/**
 * Tests for the SkillArray class.
 * @covers \App\Models\Shadowrun5E\SkillArray
 * @group models
 * @group shadowrun
 * @group shadowrun5e
 */
final class SkillArrayTest extends \Tests\TestCase
{
    /**
     * Subject under test.
     * @var SkillArray<Skill>
     */
    protected SkillArray $skills;

    /**
     * Set up a clean subject.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->skills = new SkillArray();
    }

    /**
     * Test an empty SkillArray.
     * @test
     */
    public function testEmpty(): void
    {
        self::assertEmpty($this->skills);
    }

    /**
     * Test adding a skill to the array.
     * @test
     */
    public function testAdd(): void
    {
        $this->skills[] = new ActiveSkill('automatics', 3);
        self::assertNotEmpty($this->skills);
    }

    /**
     * Test that adding a non-skill to the array throws an exception.
     * @test
     */
    public function testAddWrongTypeException(): void
    {
        self::expectException(\TypeError::class);
        // @phpstan-ignore-next-line
        $this->skills[] = new AdeptPower('improved-sense-direction-sense');
    }

    /**
     * Test that adding a non-skill to the array doesn't add it.
     * @test
     */
    public function testAddWrongTypeDoesntAdd(): void
    {
        try {
            // @phpstan-ignore-next-line
            $this->skills[] = new AdeptPower('improved-sense-direction-sense');
        } catch (\TypeError $e) {
            // Ignored
        }
        self::assertEmpty($this->skills);
    }
}
