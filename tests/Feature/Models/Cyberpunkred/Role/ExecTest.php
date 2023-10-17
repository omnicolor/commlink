<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Cyberpunkred\Role;

use App\Models\Cyberpunkred\Role\Exec;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Exec role.
 * @group cyberpunkred
 * @group models
 * @small
 */
final class ExecTest extends TestCase
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
