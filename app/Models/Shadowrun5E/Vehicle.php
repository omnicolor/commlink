<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5E;

/**
 * Class representing a vehicle in Shadowrun.
 */
class Vehicle
{
    /**
     * Acceleration rating for the vehicle.
     * @var int
     */
    public int $acceleration;

    /**
     * True if the vehicle is active (shown on character sheet).
     * @var bool
     */
    public $active;

    /**
     * Vehicle's armor rating.
     * @var int
     */
    public int $armor;

    /**
     * Collection of programs running.
     * @var ProgramArray
     */
    public ProgramArray $autosofts;

    /**
     * Availability code for the vehicle.
     * @var string
     */
    public string $availability;

    /**
     * Body rating for the vehicle.
     * @var int
     */
    public int $body;

    /**
     * Category of the vehicle.
     * @var string
     */
    public string $category;

    /**
     * Cost of the vehicle.
     * @var int
     */
    public int $cost;

    /**
     * Amount of matrix damage the vehicle has taken.
     * @var int
     */
    public int $damageMatrix;

    /**
     * Amount of physical damage the vehicle has taken.
     * @var int
     */
    public int $damagePhysical;

    /**
     * Description of the vehicle.
     * @var string
     */
    public string $description;

    /**
     * Device rating for the vehicle.
     * @var ?int
     */
    public ?int $deviceRating;

    /**
     * List of equipment added to the vehicle.
     * @var VehicleModificationArray
     */
    public VehicleModificationArray $equipment;

    /**
     * Handling rating for the vehicle.
     * @var int
     */
    public int $handling;

    /**
     * Unique ID for the vehicle.
     * @var string
     */
    public string $id;

    /**
     * Name of the vehicle.
     * @var string
     */
    public string $name;

    /**
     * List of modifications that have been made to the vehicle, including
     * stock ones.
     * @var VehicleModificationArray
     */
    public VehicleModificationArray $modifications;

    /**
     * List of modifications that came with the vehicle.
     * @var VehicleModificationArray
     */
    protected VehicleModificationArray $stockModifications;

    /**
     * Pilot rating for the vehicle.
     * @var int
     */
    public int $pilot;

    /**
     * Number of seats the vehicle has.
     * @var int
     */
    public int $seats;

    /**
     * Sensor rating for the vehicle.
     * @var int
     */
    public int $sensor;

    /**
     * Speed rating for the vehicle.
     * @var int
     */
    public int $speed;

    /**
     * Optional Subname for the vehicle.
     * @var ?string
     */
    public ?string $subname;

    /**
     * Type of vehicle (aircraft, groundcraft, etc).
     * @var string
     */
    public string $type;

    /**
     * Array of weapons on the vehicle.
     * @var WeaponArray
     */
    public WeaponArray $weapons;

    /**
     * List of all vehicles.
     * @var ?array<mixed>
     */
    public static ?array $vehicles;

    /**
     * Construct a new vehicle object.
     * @param array<string, mixed> $data Data for the vehicle to load
     * @throws \RuntimeException if the ID is invalid
     */
    public function __construct(array $data)
    {
        $filename = config('app.data_path.shadowrun5e') . 'vehicles.php';
        self::$vehicles ??= require $filename;

        $id = \strtolower($data['id']);
        if (!isset(self::$vehicles[$id])) {
            throw new \RuntimeException(
                \sprintf('Vehicle ID "%s" is invalid', $id)
            );
        }

        $vehicle = self::$vehicles[$id];
        $this->acceleration = $vehicle['acceleration'];
        $this->active = $data['active'] ?? true;
        $this->armor = $vehicle['armor'];
        $this->availability = $vehicle['availability'];
        $this->autosofts = new ProgramArray();
        $this->body = $vehicle['body'];
        $this->category = $vehicle['category'];
        $this->cost = $vehicle['cost'];
        $this->damageMatrix = $data['damageMatrix'] ?? 0;
        $this->damagePhysical = $data['damagePhysical'] ?? 0;
        $this->description = $vehicle['description'];
        $this->deviceRating = $vehicle['deviceRating'] ?? 0;
        $this->handling = $vehicle['handling'];
        $this->id = $id;
        $this->name = $vehicle['name'];
        $this->pilot = $vehicle['pilot'];
        $this->sensor = $vehicle['sensor'];
        $this->seats = $vehicle['seats'];
        $this->speed = $vehicle['speed'];
        $this->subname = $data['subname'] ?? null;
        $this->type = $vehicle['type'];

        $this->equipment = new VehicleModificationArray();
        foreach ($data['equipment'] ?? [] as $mod) {
            $this->equipment[] = new VehicleModification($mod);
        }

        $this->modifications = new VehicleModificationArray();
        $this->stockModifications = new VehicleModificationArray();
        foreach ($data['modifications'] ?? [] as $mod) {
            $this->modifications[] = new VehicleModification($mod);
        }
        foreach ($vehicle['modifications'] ?? [] as $mod) {
            $this->modifications[] = new VehicleModification($mod);
            $this->stockModifications[] = new VehicleModification($mod);
        }
        $this->weapons = new WeaponArray();
        foreach ($data['weapons'] ?? [] as $weapon) {
            $this->weapons[] = Weapon::buildWeapon($weapon);
        }
    }

    /**
     * Return the name of the vehicle.
     * @return string
     */
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * Return whether the vehicle is considered a drone.
     * @return bool
     */
    public function isDrone(): bool
    {
        return false !== \strpos($this->category, 'drone');
    }

    /**
     * Return the cost of the vehicle, including modifications.
     * @return int
     */
    public function getCost(): int
    {
        $cost = $this->cost;
        foreach ($this->modifications as $mod) {
            $cost += $mod->cost;
        }
        foreach ($this->stockModifications as $mod) {
            $cost -= $mod->cost;
        }
        foreach ($this->weapons as $weapon) {
            $cost += $weapon->getCost();
        }
        return $cost;
    }

    /**
     * Return the number of boxen in the vehicle's matrix condition monitor.
     * @return int
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
     * @return int
     */
    public function getPhysicalConditionMonitor(): int
    {
        if ($this->isDrone()) {
            return 6 + (int)\ceil($this->body / 2);
        }
        return 12 + (int)\ceil($this->body / 2);
    }
}
