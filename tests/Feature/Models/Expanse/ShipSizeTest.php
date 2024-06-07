<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Expanse;

use App\Models\Expanse\ShipSize;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

/**
 * Tests for the ship size enum.
 * @group expanse
 */
#[Small]
final class ShipSizeTest extends TestCase
{
    /**
     * Data provider for length tests.
     * @return array<int, array<ShipSize|string>>
     */
    public static function lengthProvider(): array
    {
        return [
            [ShipSize::Tiny, '5m'],
            [ShipSize::Small, '10m'],
            [ShipSize::Medium, '25m'],
            [ShipSize::Large, '50m'],
            [ShipSize::Huge, '100m'],
            [ShipSize::Gigantic, '250m'],
            [ShipSize::Colossal, '500m'],
            [ShipSize::Titanic, '1km+'],
        ];
    }

    /**
     * @dataProvider lengthProvider
     */
    public function testLength(ShipSize $size, string $length): void
    {
        self::assertSame($length, $size->length());
    }

    /**
     * Data provider for hull tests.
     * @return array<int, array<ShipSize|string>>
     */
    public static function hullProvider(): array
    {
        return [
            [ShipSize::Tiny, '1d1'],
            [ShipSize::Small, '1d3'],
            [ShipSize::Medium, '1d6'],
            [ShipSize::Large, '2d6'],
            [ShipSize::Huge, '3d6'],
            [ShipSize::Gigantic, '4d6'],
            [ShipSize::Colossal, '5d6'],
            [ShipSize::Titanic, '6d6'],
        ];
    }

    /**
     * @dataProvider hullProvider
     */
    public function testHull(ShipSize $size, string $hull): void
    {
        self::assertSame($hull, $size->hull());
    }

    /**
     * Data provider for crew tests.
     * @return array<int, array<ShipSize|int>>
     */
    public static function crewProvider(): array
    {
        return [
            [ShipSize::Tiny, 1, 2],
            [ShipSize::Small, 1, 2],
            [ShipSize::Medium, 2, 4],
            [ShipSize::Large, 4, 16],
            [ShipSize::Huge, 16, 64],
            [ShipSize::Gigantic, 64, 512],
            [ShipSize::Colossal, 256, 2048],
            [ShipSize::Titanic, 1024, 8192],
        ];
    }

    /**
     * @dataProvider crewProvider
     */
    public function testCrew(ShipSize $size, int $minimum, int $standard): void
    {
        self::assertSame($minimum, $size->crewMin());
        self::assertSame($standard, $size->crewStandard());
    }
}
