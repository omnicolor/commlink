<?php

declare(strict_types=1);

namespace Modules\Cyberpunkred\Tests\Feature\Models\Role;

use Modules\Cyberpunkred\Models\Role\Lawman;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;

#[Group('cyberpunkred')]
#[Small]
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
