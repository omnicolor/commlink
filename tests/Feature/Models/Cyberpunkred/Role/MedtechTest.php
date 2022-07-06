<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Cyberpunkred\Role;

use App\Models\Cyberpunkred\Role\Medtech;

/**
 * Tests for the Medtech role.
 * @group cyberpunkred
 * @group models
 * @small
 */
final class MedtechTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test the toString method.
     * @test
     */
    public function testToString(): void
    {
        $role = new Medtech([
            'rank' => 4,
        ]);
        self::assertSame('Medtech', (string)$role);
    }
}
