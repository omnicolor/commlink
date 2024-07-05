<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Models;

use Modules\Shadowrun5e\Models\License;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class LicenseTest extends TestCase
{
    public function testGetCost(): void
    {
        $license = new License(2, 'test');
        self::assertSame(400, $license->getCost());
        $license->rating = 6;
        self::assertSame(1200, $license->getCost());
    }

    public function testToString(): void
    {
        $license = new License(4, 'Drivers');
        self::assertSame('Drivers (4)', (string)$license);
    }
}
