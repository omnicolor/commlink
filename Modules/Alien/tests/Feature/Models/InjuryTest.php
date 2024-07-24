<?php

declare(strict_types=1);

namespace Modules\Alien\Tests\Feature\Models;

use Modules\Alien\Models\Injury;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('alien')]
#[Small]
final class InjuryTest extends TestCase
{
    public function testNotFound(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Injury ID "unknown" is invalid');
        new Injury('unknown');
    }

    public function testToString(): void
    {
        $injury = new Injury('winded');
        self::assertSame('Winded', (string)$injury);
    }

    public function testFindByRollNoMatch(): void
    {
        self::assertNull(Injury::findByRoll(17));
    }

    public function testFindByRoll(): void
    {
        $injury = Injury::findByRoll(12);
        self::assertSame('Stunned', (string)$injury);
    }

    public function testAll(): void
    {
        self::assertCount(36, Injury::all());
    }
}
