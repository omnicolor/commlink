<?php

declare(strict_types=1);

namespace Modules\Cyberpunkred\Tests\Feature\Models\Role;

use Modules\Cyberpunkred\Models\Role\Solo;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Solo role.
 */
#[Group('cyberpunkred')]
#[Small]
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
