<?php

declare(strict_types=1);

namespace Tests\Features\Models\Shadowrun5E;

use App\Models\Shadowrun5E\VehicleModification;

/**
 * Unit tests for Vehicle Modifications.
 * @covers \App\Models\Shadowrun5E\VehicleModification
 * @group models
 * @group shadowrun
 * @group shadowrun5e
 */
final class VehicleModificationTest extends \Tests\TestCase
{
    /**
     * Test trying to load an invalid modification.
     * @test
     */
    public function testLoadInvalid(): void
    {
        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage(
            'Vehicle modification "invalid" is invalid'
        );
        new VehicleModification('invalid');
    }

    /**
     * Test the constructor on a vehicle modification.
     * @test
     */
    public function testConstructorModification(): void
    {
        $mod = new VehicleModification('manual-control-override');
        self::assertSame('6', $mod->availability);
        self::assertSame(500, $mod->cost);
        self::assertNotNull($mod->description);
        self::assertSame('manual-control-override', $mod->id);
        self::assertSame('Manual Control Override', $mod->name);
        self::assertSame(154, $mod->page);
        self::assertNull($mod->rating);
        self::assertSame('rigger-5', $mod->ruleset);
        self::assertSame('Power train', $mod->slotType);
        self::assertSame(1, $mod->slots);
    }

    /**
     * Test the __toString() method.
     * @test
     */
    public function testToString(): void
    {
        $mod = new VehicleModification('manual-control-override');
        self::assertSame('Manual Control Override', (string)$mod);
    }

    /**
     * Test the constructor with a piece of equipment that has a rating.
     * @test
     */
    public function testConstructorEquipmentWithRating(): void
    {
        $mod = new VehicleModification('sensor-array-2');
        self::assertSame(2000, $mod->cost);
        self::assertSame(2, $mod->rating);
        self::assertNull($mod->slotType);
        self::assertNull($mod->slots);
    }
}
