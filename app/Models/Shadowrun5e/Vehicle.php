<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5e;

use RuntimeException;

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
 * @psalm-suppress PossiblyUnusedProperty
 */
class Vehicle
{
    /**
     * Acceleration rating for the vehicle.
     */
    public int $stockAcceleration;

    /**
     * True if the vehicle is active (shown on character sheet).
     */
    public bool $active;

    /**
     * Vehicle's armor rating.
     */
    public int $stockArmor;

    /**
     * Collection of programs running.
     */
    public ProgramArray $autosofts;

    /**
     * Availability code for the vehicle.
     */
    public string $availability;

    /**
     * Body rating for the vehicle.
     */
    public int $stockBody;

    /**
     * Category of the vehicle.
     */
    public string $category;

    /**
     * Cost of the vehicle.
     */
    public int $cost;

    /**
     * Amount of matrix damage the vehicle has taken.
     */
    public int $damageMatrix;

    /**
     * Amount of physical damage the vehicle has taken.
     */
    public int $damagePhysical;

    /**
     * Description of the vehicle.
     */
    public string $description;

    /**
     * Device rating for the vehicle.
     */
    public ?int $deviceRating;

    /**
     * List of equipment added to the vehicle.
     */
    public VehicleModificationArray $equipment;

    /**
     * Handling rating for the vehicle.
     */
    public int $stockHandling;

    /**
     * Off-road handling for the vehicle.
     */
    public ?int $stockHandlingOffRoad;

    /**
     * Unique ID for the vehicle.
     */
    public string $id;

    /**
     * Name of the vehicle.
     */
    public string $name;

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
    public int $stockPilot;

    /**
     * Number of seats the vehicle has.
     */
    public int $seats;

    /**
     * Sensor rating for the vehicle.
     */
    public int $stockSensor;

    /**
     * Speed rating for the vehicle.
     */
    public int $stockSpeed;

    /**
     * Optional Subname for the vehicle.
     */
    public ?string $subname;

    /**
     * Type of vehicle (aircraft, groundcraft, etc).
     */
    public string $type;

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
        $filename = config('app.data_path.shadowrun5e') . 'vehicles.php';
        self::$vehicles ??= require $filename;

        $id = \strtolower($data['id']);
        if (!isset(self::$vehicles[$id])) {
            throw new RuntimeException(
                \sprintf('Vehicle ID "%s" is invalid', $id)
            );
        }

        $vehicle = self::$vehicles[$id];
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
        $this->id = $id;
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

    /**
     * @psalm-suppress PossiblyUnusedReturnValue
     */
    public function __get(string $name): ?int
    {
        $property = 'stock' . ucfirst($name);
        if (!property_exists($this, $property)) {
            return null;
        }

        // @phpstan-ignore-next-line
        $attribute = $this->$property;
        foreach ($this->modifications as $mod) {
            if (0 === count($mod->effects)) {
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

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * Return whether the vehicle is considered a drone.
     */
    public function isDrone(): bool
    {
        return false !== \strpos($this->category, 'drone');
    }

    /**
     * Return the cost of the vehicle, including non-stock modifications.
     * @psalm-suppress PossiblyUnusedMethod
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
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function getMatrixConditionMonitor(): int
    {
        if (!isset($this->deviceRating)) {
            return 0;
        }
        return 8 + (int)\ceil($this->deviceRating / 2);
    }

    /**
     * Return the number of boxen in the vehicle's physical condition monitor.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function getPhysicalConditionMonitor(): int
    {
        if ($this->isDrone()) {
            return 6 + (int)\ceil($this->body / 2);
        }
        return 12 + (int)\ceil($this->body / 2);
    }
}
