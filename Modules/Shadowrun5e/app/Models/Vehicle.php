<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use Override;
use RuntimeException;
use Stringable;

use function ceil;
use function config;
use function count;
use function is_array;
use function sprintf;
use function str_contains;
use function str_starts_with;
use function strtolower;

/**
 * Class representing a vehicle in Shadowrun.
 * @property-read int $acceleration
 * @property-read int $armor
 * @property-read int $body
 * @property-read int $handling
 * @property-read ?int $handlingOffRoad
 * @property-read int $pilot
 * @property-read int $sensor
 * @property-read int $speed
 */
class Vehicle implements Stringable
{
    /**
     * Acceleration rating for the vehicle.
     */
    public readonly int $stockAcceleration;

    /**
     * True if the vehicle is active (shown on character sheet).
     */
    public bool $active = false;

    /**
     * Vehicle's armor rating.
     */
    public readonly int $stockArmor;

    /**
     * Collection of programs running.
     */
    public ProgramArray $autosofts;
    public readonly string $availability;

    /**
     * Body rating for the vehicle.
     */
    public readonly int $stockBody;

    /**
     * Category of the vehicle.
     */
    public readonly string $category;

    /**
     * Cost of the vehicle.
     */
    public readonly int $cost;

    /**
     * Amount of matrix damage the vehicle has taken.
     */
    public int $damageMatrix;

    /**
     * Amount of physical damage the vehicle has taken.
     */
    public int $damagePhysical;
    public readonly string $description;

    /**
     * Device rating for the vehicle.
     */
    public int|null $deviceRating;

    /**
     * List of equipment added to the vehicle.
     */
    public VehicleModificationArray $equipment;

    public readonly string $id;

    /**
     * Handling rating for the vehicle.
     */
    public readonly int $stockHandling;

    /**
     * Off-road handling for the vehicle.
     */
    public readonly ?int $stockHandlingOffRoad;
    public readonly string $name;

    /**
     * List of modifications that have been made to the vehicle, including
     * stock ones.
     */
    public VehicleModificationArray $modifications;

    /**
     * List of modifications that came with the vehicle.
     */
    protected VehicleModificationArray $stockModifications;

    /**
     * Pilot rating for the vehicle.
     */
    public readonly int $stockPilot;

    /**
     * Number of seats the vehicle has.
     */
    public readonly int $seats;

    /**
     * Sensor rating for the vehicle.
     */
    public readonly int $stockSensor;

    /**
     * Speed rating for the vehicle.
     */
    public readonly int $stockSpeed;

    /**
     * Optional Subname for the vehicle.
     */
    public readonly null|string $subname;

    /**
     * Type of vehicle (aircraft, groundcraft, etc).
     */
    public readonly string $type;

    /**
     * Array of weapons on the vehicle.
     */
    public WeaponArray $weapons;

    /**
     * List of all vehicles.
     * @var ?array<string, array<string, int|string>>
     */
    public static ?array $vehicles;

    /**
     * Construct a new vehicle object.
     * @param array<string, mixed> $data Data for the vehicle to load
     * @throws RuntimeException if the ID is invalid
     */
    public function __construct(array $data)
    {
        $filename = config('shadowrun5e.data_path') . 'vehicles.php';
        self::$vehicles ??= require $filename;

        $this->id = strtolower((string)$data['id']);
        if (!isset(self::$vehicles[$this->id])) {
            throw new RuntimeException(
                sprintf('Vehicle ID "%s" is invalid', $this->id)
            );
        }

        $vehicle = self::$vehicles[$this->id];
        $this->stockAcceleration = $vehicle['acceleration'];
        $this->active = $data['active'] ?? true;
        $this->stockArmor = $vehicle['armor'];
        $this->availability = $vehicle['availability'];
        $this->autosofts = new ProgramArray();
        $this->stockBody = $vehicle['body'];
        $this->category = $vehicle['category'];
        $this->cost = $vehicle['cost'];
        $this->damageMatrix = $data['damageMatrix'] ?? 0;
        $this->damagePhysical = $data['damagePhysical'] ?? 0;
        $this->description = $vehicle['description'];
        $this->deviceRating = $vehicle['deviceRating'] ?? 0;
        $this->stockHandling = $vehicle['handling'];
        $this->name = $vehicle['name'];
        $this->stockPilot = $vehicle['pilot'];
        $this->stockSensor = $vehicle['sensor'];
        $this->seats = $vehicle['seats'];
        $this->stockSpeed = $vehicle['speed'];
        $this->subname = $data['subname'] ?? null;
        $this->type = $vehicle['type'];

        // Non-ground vehicles won't have off-road handling.
        $this->stockHandlingOffRoad = $vehicle['handlingOffRoad'] ?? null;

        $this->equipment = new VehicleModificationArray();
        foreach ($data['equipment'] ?? [] as $mod) {
            $this->equipment[] = new VehicleModification($mod);
        }

        $this->weapons = new WeaponArray();
        foreach ($data['weapons'] ?? [] as $weapon) {
            $this->weapons[] = Weapon::buildWeapon($weapon);
        }

        $this->modifications = new VehicleModificationArray();
        $this->stockModifications = new VehicleModificationArray();
        foreach ($data['modifications'] ?? [] as $mod) {
            if (is_array($mod)) {
                $modification = new VehicleModification($mod['id'], $mod);
            } else {
                $modification = new VehicleModification($mod);
            }
            $this->modifications[] = $modification;
        }
        foreach ($vehicle['modifications'] ?? [] as $mod) {
            if (is_array($mod)) {
                $modification = new VehicleModification($mod['id'], $mod);
            } else {
                $modification = new VehicleModification($mod);
            }
            $this->modifications[] = $modification;
            $this->stockModifications[] = $modification;
        }
        foreach ($this->modifications as $modification) {
            if (
                str_starts_with($modification->id, 'weapon-mount')
                && 0 !== count($this->weapons)
            ) {
                $last = count($this->weapons) - 1;
                $modification->weapon = $this->weapons[$last];
                unset($this->weapons[$last]);
            }
        }
    }

    public function __get(string $name): ?int
    {
        $attribute = match ($name) {
            'acceleration' => $this->stockAcceleration,
            'armor' => $this->stockArmor,
            'body' => $this->stockBody,
            'handling' => $this->stockHandling,
            'handlingOffRoad' => $this->stockHandlingOffRoad,
            'pilot' => $this->stockPilot,
            'sensor' => $this->stockSensor,
            'speed' => $this->stockSpeed,
            default => null,
        };
        if (null === $attribute) {
            return null;
        }

        foreach ($this->modifications as $mod) {
            if ([] === $mod->effects) {
                continue;
            }

            foreach ($mod->effects as $effect => $value) {
                if ($effect === $name) {
                    $attribute += $value;
                }
            }
        }
        return $attribute;
    }

    #[Override]
    public function __toString(): string
    {
        return $this->name;
    }

    public static function findByName(string $name): Vehicle
    {
        $filename = config('shadowrun5e.data_path') . 'vehicles.php';
        self::$vehicles ??= require $filename;
        foreach (self::$vehicles ?? [] as $vehicle) {
            if (strtolower((string)$vehicle['name']) === strtolower($name)) {
                return new Vehicle(['id' => $vehicle['id']]);
            }
        }
        throw new RuntimeException(sprintf(
            'Vehicle name "%s" was not found',
            $name
        ));
    }

    /**
     * Return the cost of the vehicle, including non-stock modifications.
     */
    public function getCost(): int
    {
        $cost = $this->cost;
        foreach ($this->modifications as $mod) {
            $cost += $mod->getCost($this);
        }
        foreach ($this->stockModifications as $mod) {
            $cost -= $mod->getCost($this);
        }
        foreach ($this->weapons as $weapon) {
            $cost += $weapon->getCost();
        }
        return $cost;
    }

    /**
     * Return the number of boxen in the vehicle's matrix condition monitor.
     */
    public function getMatrixConditionMonitor(): int
    {
        if (!isset($this->deviceRating)) {
            return 0;
        }
        return 8 + (int)ceil($this->deviceRating / 2);
    }

    /**
     * Return the number of boxen in the vehicle's physical condition monitor.
     */
    public function getPhysicalConditionMonitor(): int
    {
        if ($this->isDrone()) {
            return 6 + (int)ceil($this->body / 2);
        }
        return 12 + (int)ceil($this->body / 2);
    }

    /**
     * Return whether the vehicle is considered a drone.
     */
    public function isDrone(): bool
    {
        return str_contains($this->category, 'drone');
    }
}
