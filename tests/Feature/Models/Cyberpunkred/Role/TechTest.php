<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Cyberpunkred\Role;

use App\Models\Cyberpunkred\Role\Tech;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Tech role.
 * @group cyberpunkred
 */
#[Small]
final class TechTest extends TestCase
{
    /**
     * Test the toString method.
     */
    public function testToString(): void
    {
        $role = new Tech([
            'rank' => 4,
        ]);
        self::assertSame('Tech', (string)$role);
    }
}
