<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5e;

use App\Models\Shadowrun5e\Vehicle;
use App\Models\Shadowrun5e\VehicleModification;
use App\Models\Shadowrun5e\Weapon;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class VehicleTest extends TestCase
{
    /**
     * Test trying to load an invalid vehicle.
     */
    public function testLoadInvalidVehicle(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Vehicle ID "unknown" is invalid');
        new Vehicle(['id' => 'unknown']);
    }

    /**
     * Test that the constructor sets the things it should set.
     */
    public function testConstructorVehicle(): void
    {
        $vehicle = new Vehicle(['id' => 'dodge-scoot']);
        self::assertSame(4, $vehicle->armor);
        self::assertSame(1, $vehicle->acceleration);
        self::assertSame('', $vehicle->availability);
        self::assertSame(4, $vehicle->body);
        self::assertSame('bike', $vehicle->category);
        self::assertSame(3000, $vehicle->cost);
        self::assertEmpty($vehicle->equipment);
        self::assertSame(4, $vehicle->handling);
        self::assertSame('dodge-scoot', $vehicle->id);
        // Dodge Scoot was modified with improved economy by Rigger 5.0.
        self::assertCount(1, $vehicle->modifications);
        self::assertSame('Dodge Scoot', $vehicle->name);
        self::assertSame(1, $vehicle->pilot);
        self::assertSame(1, $vehicle->sensor);
        self::assertSame(1, $vehicle->seats);
        self::assertSame(3, $vehicle->speed);
        self::assertSame('groundcraft', $vehicle->type);
    }

    /**
     * Test the constructor on a drone.
     */
    public function testConstructorDrone(): void
    {
        $vehicle = new Vehicle(['id' => 'mct-fly-spy']);
        self::assertSame(0, $vehicle->seats);
        self::assertSame('aircraft', $vehicle->type);
        self::assertSame('mini drone', $vehicle->category);
    }

    /**
     * Test loading a vehicle with some equipment.
     */
    public function testConstructorWithEquipment(): void
    {
        $vehicle = new Vehicle([
            'id' => 'dodge-scoot',
            'equipment' => ['sensor-array-2'],
        ]);
        self::assertNotEmpty($vehicle->equipment);
        self::assertInstanceOf(
            VehicleModification::class,
            $vehicle->equipment[0]
        );
    }

    /**
     * Test loading a vehicle with modifications.
     */
    public function testConstructorWithModification(): void
    {
        $vehicle = new Vehicle([
            'id' => 'dodge-scoot',
            'modifications' => ['manual-control-override'],
        ]);
        self::assertNotEmpty($vehicle->modifications);
        self::assertInstanceOf(
            VehicleModification::class,
            $vehicle->modifications[0]
        );
    }

    /**
     * Test the __toString method.
     */
    public function testToString(): void
    {
        $vehicle = new Vehicle(['id' => 'mct-fly-spy']);
        self::assertSame('MCT Fly-Spy', (string)$vehicle);
    }

    /**
     * Test the isDrone() method on a drone.
     */
    public function testIsDroneOnADrone(): void
    {
        $vehicle = new Vehicle(['id' => 'mct-fly-spy']);
        self::assertTrue($vehicle->isDrone());
    }

    /**
     * Test the isDrone() method on a normal vehicle.
     */
    public function testIsDroneOnAVehicle(): void
    {
        $vehicle = new Vehicle(['id' => 'dodge-scoot']);
        self::assertFalse($vehicle->isDrone());
    }

    /**
     * Test creating a vehicle with built-in weapons.
     */
    public function testWithWeapons(): void
    {
        // The most dangerous scooter ever...
        $vehicle = new Vehicle([
            'id' => 'dodge-scoot',
            'weapons' => [
                ['id' => 'ak-98'],
                ['id' => 'ak-98'],
            ],
        ]);
        self::assertCount(2, $vehicle->weapons);
        self::assertSame('AK-98', (string)$vehicle->weapons[0]);
    }

    /**
     * Test getting the matrix condition monitor for a vehicle without a device
     * rating.
     */
    public function testGetMatrixConditionMonitorNoDR(): void
    {
        $vehicle = new Vehicle(['id' => 'dodge-scoot']);
        $vehicle->deviceRating = null;
        self::assertSame(0, $vehicle->getMatrixConditionMonitor());
    }

    /**
     * Test getMatrixConditionMonitor() on a vehicle that has a device rating.
     */
    public function testGetMatrixConditionMonitor(): void
    {
        $vehicle = new Vehicle(['id' => 'dodge-scoot']);
        self::assertSame(9, $vehicle->getMatrixConditionMonitor());
    }

    /**
     * Test getPhysicalConditionMonitor() on a drone.
     */
    public function testGetPhysicalConditionMonitorDrone(): void
    {
        $vehicle = new Vehicle(['id' => 'mct-fly-spy']);
        self::assertSame(7, $vehicle->getPhysicalConditionMonitor());
    }

    /**
     * Test getPhysicalConditionMonitor() on a vehicle.
     */
    public function testGetPhysicalConditionMonitorVehicle(): void
    {
        $vehicle = new Vehicle(['id' => 'dodge-scoot']);
        self::assertSame(14, $vehicle->getPhysicalConditionMonitor());
    }

    /**
     * Test getCost() on an unmodified vehicle.
     */
    public function testGetCost(): void
    {
        $vehicle = new Vehicle(['id' => 'dodge-scoot']);
        self::assertSame(3000, $vehicle->getCost());
        $vehicle = new Vehicle(['id' => 'mct-fly-spy']);
        self::assertSame(2000, $vehicle->getCost());
    }

    /**
     * Test getCost() on a modified vehicle.
     */
    public function testGetCostModified(): void
    {
        $vehicle = new Vehicle(['id' => 'dodge-scoot']);
        $vehicle->modifications[] = new VehicleModification('manual-control-override');
        self::assertSame(3500, $vehicle->getCost());
        $vehicle->modifications[] = new VehicleModification('sensor-array-2');
        self::assertSame(5500, $vehicle->getCost());
    }

    /**
     * Test getCost() on a vehicle with weapons.
     */
    public function testGetCostWeapons(): void
    {
        $vehicle = new Vehicle(['id' => 'dodge-scoot']);
        $vehicle->weapons[] = new Weapon('ak-98');
        self::assertSame(4250, $vehicle->getCost());
    }

    /**
     * Test getCost() on a vehicle with attribute-dependant modifications.
     */
    public function testGetCostFancyModification(): void
    {
        $vehicle = new Vehicle([
            'id' => 'dodge-scoot',
            'modifications' => [
                'acceleration-enhancement-1',
            ],
        ]);
        self::assertSame(
            3000 + (10000 * 1),
            $vehicle->getCost()
        );

        $vehicle = new Vehicle([
            'id' => 'mct-fly-spy',
            'modifications' => [
                'acceleration-enhancement-1',
            ],
        ]);
        self::assertSame(
            2000 + (10000 * 2),
            $vehicle->getCost()
        );
    }

    public function testModificationEffect(): void
    {
        $vehicle = new Vehicle(['id' => 'dodge-scoot']);
        self::assertSame(1, $vehicle->acceleration);

        $vehicle->modifications[] =
            new VehicleModification('acceleration-enhancement-1');
        self::assertSame(2, $vehicle->acceleration);
    }

    public function testGettingUnknownProperty(): void
    {
        $vehicle = new Vehicle(['id' => 'dodge-scoot']);
        // @phpstan-ignore-next-line
        self::assertNull($vehicle->unknown);
    }

    public function testGetAllVehicleProperties(): void
    {
        $vehicle = new Vehicle(['id' => 'dodge-scoot']);
        self::assertSame(1, $vehicle->acceleration);
        self::assertSame(4, $vehicle->armor);
        self::assertSame(4, $vehicle->body);
        self::assertSame(4, $vehicle->handling);
        self::assertSame(3, $vehicle->handlingOffRoad);
        self::assertSame(1, $vehicle->pilot);
        self::assertSame(1, $vehicle->sensor);
        self::assertSame(3, $vehicle->speed);
    }

    public function testVehicleWithModifiedVehicleModification(): void
    {
        $vehicle = new Vehicle([
            'id' => 'dodge-scoot',
            'modifications' => [
                [
                    'id' => 'weapon-mount-standard',
                    'modifications' => [
                        'visibility-internal',
                    ],
                    'weapon' => [
                        'id' => 'ares-predator-v',
                    ],
                ],
            ],
        ]);

        $modifications = $vehicle->modifications;
        // Dodge Scoot has improved economy standard.
        self::assertCount(2, $modifications);

        /** @var VehicleModification */
        $mod = $modifications[0];
        self::assertSame('Weapon mount, standard', $mod->name);
        self::assertCount(1, $mod->modifications);
        // @phpstan-ignore-next-line
        self::assertSame('Ares Predator V', $mod->weapon->name);
    }

    /**
     * Test a vehicle with a more complicated stock modification loadout.
     */
    public function testVehicleWithStockWeaponMounts(): void
    {
        $vehicle = new Vehicle([
            'id' => 'nissan-hound',
            'weapons' => [
                ['id' => 'ak-98'],
                ['id' => 'ares-predator-v'],
            ],
        ]);

        $modifications = $vehicle->modifications;
        self::assertCount(2, $modifications);

        /** @var VehicleModification */
        $mount1 = $vehicle->modifications[0];
        self::assertSame('Weapon mount, standard', $mount1->name);
        // @phpstan-ignore-next-line
        self::assertSame('Ares Predator V', $mount1->weapon->name);

        /** @var VehicleModification */
        $mount2 = $vehicle->modifications[1];
        // @phpstan-ignore-next-line
        self::assertSame('AK-98', $mount2->weapon->name);

        self::assertEmpty($vehicle->weapons);
    }

    public function testFindByNameNotFound(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Vehicle name "Not Found" was not found');
        Vehicle::findByName('Not Found');
    }

    public function testFindByName(): void
    {
        $vehicle = Vehicle::findByName('Dodge Scoot');
        self::assertSame('dodge-scoot', $vehicle->id);
    }
}
