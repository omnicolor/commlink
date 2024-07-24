<?php

declare(strict_types=1);

namespace Modules\Alien\Tests\Feature\Models;

use Modules\Alien\Models\Gear;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('alien')]
#[Small]
final class GearTest extends TestCase
{
    public function testLoadNotFound(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Gear ID "unknown" is invalid');
        new Gear('unknown');
    }

    public function testToString(): void
    {
        $item = new Gear('m314-motion-tracker');
        self::assertSame('M314 Motion Tracker', (string)$item);
        self::assertSame(1, $item->quantity);
    }

    public function testConstructorQuantity(): void
    {
        $item = new Gear('m314-motion-tracker', 42);
        self::assertSame(42, $item->quantity);
    }

    public function testAll(): void
    {
        self::assertCount(5, Gear::all());
    }
}
