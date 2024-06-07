<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Cyberpunkred\Role;

use App\Models\Cyberpunkred\Role\Solo;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Solo role.
 * @group cyberpunkred
 * @group models
 * @small
 */
final class SoloTest extends TestCase
{
    /**
     * Test the toString method.
     */
    public function testToString(): void
    {
        $role = new Solo([
            'rank' => 4,
        ]);
        self::assertSame('Solo', (string)$role);
    }
}
