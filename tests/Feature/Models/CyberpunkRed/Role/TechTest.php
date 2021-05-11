<?php

declare(strict_types=1);

namespace Tests\Feature\Models\CyberpunkRed\Role;

use App\Models\CyberpunkRed\Role\Tech;

/**
 * Tests for the Tech role.
 * @covers App\Models\CyberpunkRed\Role
 * @covers App\Models\CyberpunkRed\Role\Tech
 * @group cyberpunkred
 * @group models
 * @small
 */
final class TechTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test the toString method.
     * @test
     */
    public function testToString(): void
    {
        $role = new Tech([
            'rank' => 4,
        ]);
        self::assertSame('Tech', (string)$role);
    }
}
