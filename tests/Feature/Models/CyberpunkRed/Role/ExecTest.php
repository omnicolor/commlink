<?php

declare(strict_types=1);

namespace Tests\Feature\Models\CyberpunkRed\Role;

use App\Models\CyberpunkRed\Role\Exec;

/**
 * Tests for the Exec role.
 * @covers App\Models\CyberpunkRed\Role
 * @covers App\Models\CyberpunkRed\Role\Exec
 * @group cyberpunkred
 * @group models
 * @small
 */
final class ExecTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test the toString method.
     * @test
     */
    public function testToString(): void
    {
        $role = new Exec([
            'rank' => 4,
        ]);
        self::assertSame('Exec', (string)$role);
    }
}
