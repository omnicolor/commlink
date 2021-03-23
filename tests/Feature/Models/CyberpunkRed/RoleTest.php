<?php

declare(strict_types=1);

namespace Tests\Feature\Models\CyberpunkRed;

use App\Models\CyberpunkRed\Role;
use App\Models\CyberpunkRed\Role\Fixer;

/**
 * Tests for the Role abstract class.
 * @covers \App\Models\CyberpunkRed\Role
 * @group cyberpunkred
 * @group models
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
}
