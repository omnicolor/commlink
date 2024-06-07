<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Cyberpunkred\Role;

use App\Models\Cyberpunkred\Role\Lawman;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Lawman role.
 * @group cyberpunkred
 * @group models
 * @small
 */
final class LawmanTest extends TestCase
{
    public function testToString(): void
    {
        $role = new Lawman([
            'rank' => 4,
        ]);
        self::assertSame('Lawman', (string)$role);
    }
}
