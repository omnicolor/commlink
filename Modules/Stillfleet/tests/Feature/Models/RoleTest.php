<?php

declare(strict_types=1);

namespace Modules\Stillfleet\Tests\Feature\Models;

use Modules\Stillfleet\Models\Role;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('stillfleet')]
#[Small]
final class RoleTest extends TestCase
{
    public function testLoadInvalid(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Role ID "invalid" is invalid');
        new Role('invalid', 1);
    }

    public function testConstructor(): void
    {
        $role = new Role('banshee', 1);
        self::assertSame('Banshee', (string)$role);
        self::assertStringContainsString('daredevil', $role->description);
        self::assertSame(['movement', 'reason'], $role->grit);
        self::assertCount(3, $role->responsibilities);
    }

    public function testAll(): void
    {
        self::assertNotEmpty(Role::all());
        self::assertInstanceOf(Role::class, Role::all()[0]);
    }

    public function testPowersEmpty(): void
    {
        $role = new Role('banshee', 1);
        self::assertCount(3, $role->powers());
    }

    public function testPowersWithOptional(): void
    {
        $role = new Role('banshee', 1, ['astrogate']);
        self::assertCount(4, $role->powers());
    }
}
