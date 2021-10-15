<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5E;

use App\Models\Shadowrun5E\License;

/**
 * Tests for License class.
 * @group models
 * @group shadowrun
 * @group shadowrun5e
 * @small
 */
final class LicenseTest extends \PHPUnit\Framework\TestCase
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
