<?php

declare(strict_types=1);

namespace Modules\Cyberpunkred\Tests\Feature\Models;

use Modules\Cyberpunkred\Models\Role;
use Modules\Cyberpunkred\Models\Role\Fixer;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;
use RuntimeException;

#[Group('cyberpunkred')]
#[Small]
final class RoleTest extends TestCase
{
    /**
     * Test building a role that doesnt exist.
     */
    public function testBuildInvalidRole(): void
    {
        self::expectException(RuntimeException::class);
        Role::fromArray(['role' => 'invalid']);
    }

    /**
     * Test building a valid role.
     */
    public function testBuildValid(): void
    {
        $role = Role::fromArray(['role' => 'fixer', 'rank' => 1, 'type' => 1]);
        self::assertInstanceOf(Fixer::class, $role);
    }

    /**
     * Test getting all of the roles.
     */
    public function testAll(): void
    {
        self::assertCount(10, Role::all());
    }
}
