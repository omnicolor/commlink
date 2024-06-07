<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Cyberpunkred\Role;

use App\Models\Cyberpunkred\Role\Medtech;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Medtech role.
 * @group cyberpunkred
 */
#[Small]
final class MedtechTest extends TestCase
{
    public function testToString(): void
    {
        $role = new Medtech([
            'rank' => 4,
        ]);
        self::assertSame('Medtech', (string)$role);
    }
}
