<?php

declare(strict_types=1);

namespace Tests\Feature\Models\CyberpunkRed\Role;

use App\Models\CyberpunkRed\Role\Netrunner;

/**
 * Tests for the Netrunner role.
 * @group cyberpunkred
 * @group models
 * @small
 */
final class NetrunnerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test the toString method.
     * @test
     */
    public function testToString(): void
    {
        $role = new Netrunner([
            'rank' => 4,
        ]);
        self::assertSame('Netrunner', (string)$role);
    }
}
