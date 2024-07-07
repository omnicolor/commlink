<?php

declare(strict_types=1);

namespace Modules\Subversion\Tests\Feature\Models;

use Modules\Subversion\Models\Skill;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('subversion')]
#[Small]
final class SkillTest extends TestCase
{
    public function testNotFound(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Skill "not-found" not found');
        new Skill('not-found');
    }

    public function testConstructor(): void
    {
        $skill = new Skill('observation');
        self::assertSame('Observation', (string)$skill);
        self::assertNull($skill->rank);

        $skill = new Skill('piloting', 1);
        self::assertSame(1, $skill->rank);
    }

    public function testAll(): void
    {
        $skills = Skill::all();
        self::assertCount(12, $skills);
    }
}
