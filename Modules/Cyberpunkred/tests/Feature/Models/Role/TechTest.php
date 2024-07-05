<?php

declare(strict_types=1);

namespace Modules\Cyberpunkred\Tests\Feature\Models\Role;

use Modules\Cyberpunkred\Models\Role\Tech;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;

#[Group('cyberpunkred')]
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
