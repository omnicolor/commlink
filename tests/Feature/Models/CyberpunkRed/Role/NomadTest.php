<?php

declare(strict_types=1);

namespace Tests\Feature\Models\CyberpunkRed\Role;

use App\Models\CyberpunkRed\Role\Nomad;

/**
 * Tests for the Nomad role.
 * @covers App\Models\CyberpunkRed\Role
 * @covers App\Models\CyberpunkRed\Role\Nomad
 * @group cyberpunkred
 * @group models
 * @small
 */
final class NomadTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test the toString method.
     * @test
     */
    public function testToString(): void
    {
        $role = new Nomad([
            'rank' => 4,
        ]);
        self::assertSame('Nomad', (string)$role);
    }
}
