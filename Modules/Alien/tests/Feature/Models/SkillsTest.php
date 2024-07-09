<?php

declare(strict_types=1);

namespace Modules\Alien\Tests\Feature\Models;

use Modules\Alien\Models\Skill;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('alien')]
#[Small]
final class SkillsTest extends TestCase
{
    public function testLoadNotFound(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Skill ID "unknown" is invalid');
        new Skill('unknown');
    }

    public function testToString(): void
    {
        $skill = new Skill('heavy-machinery');
        self::assertSame('Heavy machinery', (string)$skill);
    }

    public function testAll(): void
    {
        self::assertCount(12, Skill::all());
    }
}
