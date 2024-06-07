<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Capers;

use App\Models\Capers\Skill;
use App\Models\Capers\SkillArray;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;
use TypeError;
use stdClass;

/**
 * Tests for the SkillArray class.
 * @group capers
 */
#[Small]
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
     */
    public function testEmpty(): void
    {
        self::assertEmpty($this->skills);
    }

    /**
     * Test adding a skill to the array.
     */
    public function testAdd(): void
    {
        $this->skills[] = new Skill('guns');
        self::assertNotEmpty($this->skills);
    }

    /**
     * Test that adding a non-skill to the array throws an exception.
     */
    public function testAddWrongTypeException(): void
    {
        self::expectException(TypeError::class);
        // @phpstan-ignore-next-line
        $this->skills[] = new stdClass();
    }

    /**
     * Test that adding a non-skill to the array doesn't add it.
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
