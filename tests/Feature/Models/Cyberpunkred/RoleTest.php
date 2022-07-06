<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Cyberpunkred;

use App\Models\Cyberpunkred\Role;
use App\Models\Cyberpunkred\Role\Fixer;

/**
 * Tests for the Role abstract class.
 * @group cyberpunkred
 * @group models
 * @small
 */
final class RoleTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test building a role that doesnt exist.
     * @test
     */
    public function testBuildInvalidRole(): void
    {
        self::expectException(\RuntimeException::class);
        Role::fromArray(['role' => 'invalid']);
    }

    /**
     * Test building a valid role.
     * @test
     */
    public function testBuildValid(): void
    {
        $role = Role::fromArray(['role' => 'fixer', 'rank' => 1, 'type' => 1]);
        self::assertInstanceOf(Fixer::class, $role);
    }

    /**
     * Test getting all of the roles.
     * @test
     */
    public function testAll(): void
    {
        self::assertCount(10, Role::all());
    }
}
