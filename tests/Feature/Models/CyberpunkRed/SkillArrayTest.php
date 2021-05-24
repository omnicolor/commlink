<?php

declare(strict_types=1);

namespace Tests\Feature\Models\CyberpunkRed;

use App\Models\CyberpunkRed\Skill;
use App\Models\CyberpunkRed\SkillArray;

/**
 * Tests for the SkillArray class.
 * @covers \App\Models\CyberpunkRed\SkillArray
 * @group cyberpunkred
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
        self::expectException(\TypeError::class);
        // @phpstan-ignore-next-line
        $this->skills[] = new \StdClass();
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
            $this->skills[] = new \StdClass();
        } catch (\TypeError $e) {
            // Ignored
        }
        self::assertEmpty($this->skills);
    }
}
