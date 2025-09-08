<?php

declare(strict_types=1);

namespace Modules\Expanse\Tests\Feature\Enums;

use Iterator;
use Modules\Expanse\Enums\ShipSize;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('expanse')]
#[Small]
final class ShipSizeTest extends TestCase
{
    /**
     * Data provider for length tests.
     * @return Iterator<int, array<(ShipSize | string)>>
     */
    public static function lengthProvider(): Iterator
    {
        yield [ShipSize::Tiny, '5m'];
        yield [ShipSize::Small, '10m'];
        yield [ShipSize::Medium, '25m'];
        yield [ShipSize::Large, '50m'];
        yield [ShipSize::Huge, '100m'];
        yield [ShipSize::Gigantic, '250m'];
        yield [ShipSize::Colossal, '500m'];
        yield [ShipSize::Titanic, '1km+'];
    }

    #[DataProvider('lengthProvider')]
    public function testLength(ShipSize $size, string $length): void
    {
        self::assertSame($length, $size->length());
    }

    /**
     * Data provider for hull tests.
     * @return Iterator<int, array<(ShipSize | string)>>
     */
    public static function hullProvider(): Iterator
    {
        yield [ShipSize::Tiny, '1d1'];
        yield [ShipSize::Small, '1d3'];
        yield [ShipSize::Medium, '1d6'];
        yield [ShipSize::Large, '2d6'];
        yield [ShipSize::Huge, '3d6'];
        yield [ShipSize::Gigantic, '4d6'];
        yield [ShipSize::Colossal, '5d6'];
        yield [ShipSize::Titanic, '6d6'];
    }

    #[DataProvider('hullProvider')]
    public function testHull(ShipSize $size, string $hull): void
    {
        self::assertSame($hull, $size->hull());
    }

    /**
     * Data provider for crew tests.
     * @return Iterator<int, array<(int | ShipSize)>>
     */
    public static function crewProvider(): Iterator
    {
        yield [ShipSize::Tiny, 1, 2];
        yield [ShipSize::Small, 1, 2];
        yield [ShipSize::Medium, 2, 4];
        yield [ShipSize::Large, 4, 16];
        yield [ShipSize::Huge, 16, 64];
        yield [ShipSize::Gigantic, 64, 512];
        yield [ShipSize::Colossal, 256, 2048];
        yield [ShipSize::Titanic, 1024, 8192];
    }

    #[DataProvider('crewProvider')]
    public function testCrew(ShipSize $size, int $minimum, int $standard): void
    {
        self::assertSame($minimum, $size->crewMin());
        self::assertSame($standard, $size->crewStandard());
    }
}
