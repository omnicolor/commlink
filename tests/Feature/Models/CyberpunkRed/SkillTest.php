<?php

declare(strict_types=1);

namespace Tests\Feature\Models\CyberpunkRed;

use App\Models\CyberpunkRed\Character;
use App\Models\CyberpunkRed\Skill;

/**
 * Unit tests for the skill class.
 * @covers \App\Models\CyberpunkRed\Skill
 * @group cyberpunkred
 * @group models
 */
final class SkillTest extends \Tests\TestCase
{
    /**
     * Test trying to load an invalid skill throws an exception.
     * @test
     */
    public function testLoadingInvalidSkill(): void
    {
        Skill::$skills = null;
        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('Skill ID "not-found-id" is invalid');
        new Skill('not-found-id', 0);
    }

    /**
     * Test that loading a skill sets all of the fields.
     * @test
     */
    public function testLoadingSetsFields(): void
    {
        $skill = new Skill('business');
        self::assertSame('intelligence', $skill->attribute);
        self::assertSame('Education', $skill->category);
        self::assertNotEmpty($skill->description);
        self::assertNotEmpty($skill->examples);
        self::assertSame('business', $skill->id);
        self::assertSame(0, $skill->level);
        self::assertSame('Business', $skill->name);
        self::assertSame(133, $skill->page);
    }

    /**
     * Test the __toString() method.
     * @test
     */
    public function testToString(): void
    {
        $skill = new Skill('business');
        self::assertSame('Business', (string)$skill);
    }

    /**
     * Test getBase().
     * @test
     */
    public function testGetBase(): void
    {
        $intelligence = random_int(1, 8);
        $level = random_int(1, 8);
        $character = new Character(['intelligence' => $intelligence]);
        $skill = new Skill('business', $level);
        self::assertSame($intelligence + $level, $skill->getBase($character));
    }
}
