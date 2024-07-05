<?php

declare(strict_types=1);

namespace Modules\Cyberpunkred\Tests\Feature\Models\Role;

use Modules\Cyberpunkred\Models\Role\Netrunner;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;

#[Group('cyberpunkred')]
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
