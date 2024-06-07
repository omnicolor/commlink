<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Cyberpunkred\Role;

use App\Models\Cyberpunkred\Role\Netrunner;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Netrunner role.
 * @group cyberpunkred
 */
#[Small]
final class NetrunnerTest extends TestCase
{
    public function testToString(): void
    {
        $role = new Netrunner([
            'rank' => 4,
        ]);
        self::assertSame('Netrunner', (string)$role);
    }
}
