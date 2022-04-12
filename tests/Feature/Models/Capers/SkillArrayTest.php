<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Capers;

use App\Models\Capers\Skill;
use App\Models\Capers\SkillArray;

/**
 * Tests for the SkillArray class.
 * @covers \App\Models\Capers\SkillArray
 * @group capers
 * @group models
 * @small
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
        $this->skills[] = new Skill('guns');
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
        $this->skills[] = new \StdClass();
    }

    /**
     * Test that adding a non-skill to the array doesn't add it.
     * @test
     */
    public function testAddWrongTypeDoesntAdd(): void
    {
        try {
            // @phpstan-ignore-next-line
            $this->skills->offsetSet(skill: new \StdClass());
        } catch (\TypeError $e) {
            // Ignored
        }
        self::assertEmpty($this->skills);
    }
}
