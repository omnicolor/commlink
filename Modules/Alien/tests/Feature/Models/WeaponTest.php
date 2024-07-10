<?php

declare(strict_types=1);

namespace Modules\Alien\Tests\Feature\Models;

use Modules\Alien\Models\Weapon;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('alien')]
#[Small]
final class WeaponTest extends TestCase
{
    public function testLoadNotFound(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Weapon ID "unknown" is invalid');
        new Weapon('unknown');
    }

    public function testToString(): void
    {
        $weapon = new Weapon('m4a3-service-pistol');
        self::assertSame('M4A3 Service Pistol', (string)$weapon);
    }

    public function testAll(): void
    {
        self::assertCount(2, Weapon::all());
    }
}
