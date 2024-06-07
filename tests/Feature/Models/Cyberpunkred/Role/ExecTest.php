<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Cyberpunkred\Role;

use App\Models\Cyberpunkred\Role\Exec;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Exec role.
 * @group cyberpunkred
 */
#[Small]
final class ExecTest extends TestCase
{
    /**
     * Test the toString method.
     */
    public function testToString(): void
    {
        $role = new Exec([
            'rank' => 4,
        ]);
        self::assertSame('Exec', (string)$role);
    }
}
