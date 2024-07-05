<?php

declare(strict_types=1);

namespace Modules\Cyberpunkred\Tests\Feature\Models\Role;

use Modules\Cyberpunkred\Models\Role\Media;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;

#[Group('cyberpunkred')]
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
