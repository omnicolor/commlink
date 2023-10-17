<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Cyberpunkred;

use App\Models\Cyberpunkred\Skill;
use App\Models\Cyberpunkred\SkillArray;
use Tests\TestCase;
use TypeError;
use stdClass;

/**
 * Tests for the SkillArray class.
 * @group cyberpunkred
 * @group models
 * @small
 */
final class SkillArrayTest extends TestCase
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
        $this->skills[] = new Skill('business');
        self::assertNotEmpty($this->skills);
    }

    /**
     * Test that adding something other than a skill to the array throws an
     * exception.
     * @test
     */
    public function testAddWrongTypeThrowsException(): void
    {
        self::expectException(TypeError::class);
        // @phpstan-ignore-next-line
        $this->skills[] = new stdClass();
    }

    /**
     * Test that adding something other than a skill to the array doesn't add
     * anything.
     * @test
     */
    public function testAddWrongTypeDoesntAdd(): void
    {
        try {
            // @phpstan-ignore-next-line
            $this->skills->offsetSet(skill: new stdClass());
        } catch (TypeError) {
            // Ignored
        }
        self::assertEmpty($this->skills);
    }
}
