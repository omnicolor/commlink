<?php

declare(strict_types=1);

namespace Modules\Subversion\Tests\Feature\Models;

use Modules\Subversion\Models\Gear;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('subversion')]
#[Small]
final class GearTest extends TestCase
{
    public function testLoadNotFound(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Gear "not-found" not found');
        new Gear('not-found');
    }

    public function testLoadWithoutFirewall(): void
    {
        $gear = new Gear('auto-intruder');
        self::assertSame('Auto-intruder', (string)$gear);
        self::assertSame(3, $gear->fortune);
        self::assertNull($gear->firewall);
        self::assertNull($gear->security_rating);
    }

    public function testLoadWithFirewall(): void
    {
        $gear = new Gear('paylo');
        self::assertSame(0, $gear->firewall);
        self::assertSame(8, $gear->security_rating);
    }

    public function testAll(): void
    {
        $gear = Gear::all();
        self::assertCount(2, $gear);
    }
}
