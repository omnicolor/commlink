<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Cyberpunkred\Role;

use App\Models\Cyberpunkred\Role\Media;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Media role.
 * @group cyberpunkred
 */
#[Small]
final class MediaTest extends TestCase
{
    /**
     * Test the toString method.
     */
    public function testToString(): void
    {
        $role = new Media([
            'rank' => 4,
        ]);
        self::assertSame('Media', (string)$role);
    }
}
