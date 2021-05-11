<?php

declare(strict_types=1);

namespace Tests\Feature\Models\CyberpunkRed\Role;

use App\Models\CyberpunkRed\Role\Solo;

/**
 * Tests for the Solo role.
 * @covers App\Models\CyberpunkRed\Role
 * @covers App\Models\CyberpunkRed\Role\Solo
 * @group cyberpunkred
 * @group models
 * @small
 */
final class SoloTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test the toString method.
     * @test
     */
    public function testToString(): void
    {
        $role = new Solo([
            'rank' => 4,
        ]);
        self::assertSame('Solo', (string)$role);
    }
}
