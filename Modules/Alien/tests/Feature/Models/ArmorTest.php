<?php

declare(strict_types=1);

namespace Modules\Alien\Tests\Feature\Models;

use Modules\Alien\Models\Armor;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('alien')]
#[Small]
final class ArmorTest extends TestCase
{
    public function testLoadNotFound(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Armor ID "unknown" is invalid');
        new Armor('unknown');
    }

    public function testToString(): void
    {
        $armor = new Armor('m3-personnel-armor');
        self::assertSame('M3 Personnel Armor', (string)$armor);
    }

    public function testAll(): void
    {
        self::assertCount(2, Armor::all());
    }
}
