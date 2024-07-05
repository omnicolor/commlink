<?php

declare(strict_types=1);

namespace Modules\Cyberpunkred\Tests\Feature\Models\Role;

use Modules\Cyberpunkred\Models\Role\Nomad;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;

#[Group('cyberpunkred')]
#[Small]
final class NomadTest extends TestCase
{
    /**
     * Test the toString method.
     */
    public function testToString(): void
    {
        $role = new Nomad([
            'rank' => 4,
        ]);
        self::assertSame('Nomad', (string)$role);
    }
}
