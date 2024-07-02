<?php

declare(strict_types=1);

namespace Tests\Feature\Models\StarTrekAdventures;

use App\Models\StarTrekAdventures\Talent;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('startrekadventures')]
#[Small]
final class TalentTest extends TestCase
{
    public function testFindNotFound(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Talent ID "not-found" is invalid');
        new Talent('not-found');
    }

    public function testFind(): void
    {
        $talent = new Talent('bold-command');
        self::assertSame(135, $talent->page);
        self::assertSame('core', $talent->ruleset);
        self::assertSame('Bold - Command', (string)$talent);
        self::assertEmpty($talent->requirements);
        self::assertNotEmpty($talent->incompatibleWith);
    }

    public function testToStringWithExtra(): void
    {
        $talent = new Talent('bold-command', ', not a pilot!');
        self::assertSame('Bold - Command , not a pilot!', (string)$talent);
    }
}
