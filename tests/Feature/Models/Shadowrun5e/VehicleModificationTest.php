<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5e;

use App\Models\Shadowrun5e\Vehicle;
use App\Models\Shadowrun5e\VehicleModification;
use App\Models\Shadowrun5e\VehicleModificationSlotType;

/**
 * Unit tests for Vehicle Modifications.
 * @group models
 * @group shadowrun
 * @group shadowrun5e
 * @small
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
        self::assertSame(VehicleModificationSlotType::PowerTrain, $mod->slotType);
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

    /**
     * Test getting the cost of a simple modification that doesn't change cost
     * based on the attributes of the vehicle it's installed on.
     * @test
     */
    public function testGetCostNoFormula(): void
    {
        $mod = new VehicleModification('manual-control-override');

        $vehicle = new Vehicle(['id' => 'dodge-scoot']);
        self::assertSame(500, $mod->getCost($vehicle));

        $vehicle = new Vehicle(['id' => 'mct-fly-spy']);
        self::assertSame(500, $mod->getCost($vehicle));
    }

    /**
     * Test getting the cost of a modification that changes cost based on the
     * attributes of the vehicle it's installed on.
     * @test
     */
    public function testGetCostWithFormula(): void
    {
        $mod = new VehicleModification('acceleration-enhancement-1');

        $vehicle = new Vehicle(['id' => 'dodge-scoot']);
        self::assertSame(10000, $mod->getCost($vehicle));

        $vehicle = new Vehicle(['id' => 'mct-fly-spy']);
        self::assertSame(20000, $mod->getCost($vehicle));
    }

    /**
     * Test getting the cost of a modification that is based on the cost of the
     * vehicle.
     * @test
     */
    public function testGetCostMultiplied(): void
    {
        $mod = new VehicleModification('off-road-suspension');

        $vehicle = new Vehicle(['id' => 'dodge-scoot']);
        self::assertSame(750, $mod->getCost($vehicle));

        // Off-road suspension on a Fly Spy? Why not?
        $vehicle = new Vehicle(['id' => 'mct-fly-spy']);
        self::assertSame(500, $mod->getCost($vehicle));
    }

    /**
     * Test checking whether a modification is allowed if there are no
     * requirements.
     * @test
     */
    public function testIsAllowedNoRequirements(): void
    {
        $mod = new VehicleModification('sensor-array-2');

        self::assertTrue($mod->isAllowed(new Vehicle(['id' => 'mct-fly-spy'])));
        self::assertTrue($mod->isAllowed(new Vehicle(['id' => 'dodge-scoot'])));
    }

    /**
     * Test the isAllowed method on a couple of vehicles.
     * @test
     */
    public function testIsAllowedWithRequirements(): void
    {
        // Gecko tips for small vehicles require body three or less.
        $mod = new VehicleModification('gecko-tips-small');

        self::assertTrue($mod->isAllowed(new Vehicle(['id' => 'mct-fly-spy'])));
        self::assertFalse($mod->isAllowed(new Vehicle(['id' => 'dodge-scoot'])));
    }

    /**
     * Test a vehicle modification that is itself modified.
     * @test
     */
    public function testModifiedModification(): void
    {
        $rawMod = [
            'id' => 'weapon-mount-standard',
            'modifications' => [
                'visibility-internal',
            ],
            'weapon' => [
                'id' => 'ares-predator-v',
            ],
        ];
        $mod = new VehicleModification($rawMod['id'], $rawMod);

        self::assertSame(4, $mod->getSlots());
    }

    /**
     * Test getting the slots of a modification that doesn't use slots.
     * @test
     */
    public function testGetSlotsOnEquipment(): void
    {
        $mod = new VehicleModification('rigger-interface');
        self::assertNull($mod->getSlots());
    }
}
