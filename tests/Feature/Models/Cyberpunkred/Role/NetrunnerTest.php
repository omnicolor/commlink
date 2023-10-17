<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Cyberpunkred\Role;

use App\Models\Cyberpunkred\Role\Netrunner;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Netrunner role.
 * @group cyberpunkred
 * @group models
 * @small
 */
final class NetrunnerTest extends TestCase
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
