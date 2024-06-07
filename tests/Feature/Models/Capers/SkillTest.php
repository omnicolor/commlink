<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Capers;

use App\Models\Capers\Skill;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

/**
 * Tests for Capers skills.
 * @group capers
 */
#[Small]
final class SkillTest extends TestCase
{
    /**
     * Test trying to initialize an invalid skill.
     */
    public function testInvalidSkill(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Skill ID "invalid" is invalid');
        new Skill('invalid');
    }

    /**
     * Test constructing a valid skill.
     */
    public function testSkill(): void
    {
        $skill = new Skill('GUNS');
        self::assertSame('Guns', (string)$skill);
        self::assertSame('guns', $skill->id);
        self::assertSame(
            'Guns covers all manner of firearms, from pistols to Tommy guns.',
            $skill->description
        );
    }

    /**
     * Test getting all skills.
     */
    public function testAll(): void
    {
        $skills = Skill::all();
        self::assertNotEmpty($skills);
        self::assertSame('Sense', (string)$skills['sense']);
    }
}
