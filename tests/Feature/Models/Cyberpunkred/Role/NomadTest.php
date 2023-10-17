<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Cyberpunkred\Role;

use App\Models\Cyberpunkred\Role\Nomad;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Nomad role.
 * @group cyberpunkred
 * @group models
 * @small
 */
final class NomadTest extends TestCase
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
