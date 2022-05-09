<?php

declare(strict_types=1);

namespace Tests\Feature\Models\CyberpunkRed\Role;

use App\Models\CyberpunkRed\Role\Media;

/**
 * Tests for the Media role.
 * @group cyberpunkred
 * @group models
 * @small
 */
final class MediaTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test the toString method.
     * @test
     */
    public function testToString(): void
    {
        $role = new Media([
            'rank' => 4,
        ]);
        self::assertSame('Media', (string)$role);
    }
}
