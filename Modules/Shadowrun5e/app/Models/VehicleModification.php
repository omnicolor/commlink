<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use RuntimeException;
use Stringable;

use function assert;
use function config;
use function sprintf;
use function strtolower;

/**
 * Something to add to a vehicle.
 */
class VehicleModification implements Stringable
{
    /**
     * Availability code for the modification.
     */
    public string $availability;

    /**
     * Cost of the modification.
     */
    public ?int $cost;

    /**
     * Attribute of the vehicle to multiply the cost.
     */
    public ?string $costAttribute;

    /**
     * Cost multiplier to use based on the vehicle's cost.
     */
    public ?float $costMultiplier = null;

    /**
     * Description of the modification.
     */
    public string $description;

    /**
     * Collection of effects after installing the modification.
     * @var array<string, int>
     */
    public array $effects;

    /**
     * Some vehicle modifications can take modifications. Weapons mounts, for
     * example.
     */
    public VehicleModificationArray $modifications;

    /**
     * Name of the modification.
     */
    public string $name;

    /**
     * Page the modification is found on.
     */
    public int $page;

    /**
     * Rating of the modification.
     */
    public ?int $rating;

    /**
     * Requirements for the modification to be valid for a vehicle.
     * @var array<int, callable>
     */
    public array $requirements;

    /**
     * Ruleset identifier.
     */
    public string $ruleset;

    /**
     * For modifications, what type of slot it takes: power-train, protection,
     * weapons, body, electromagnetic, or cosmetic.
     */
    public ?VehicleModificationSlotType $slotType;

    /**
     * For modifications, how many slots it takes.
     */
    public ?int $slots;

    /**
     * Type of modification: equipment, vehicle-mod, modification-mod.
     */
    public ?VehicleModificationType $type;

    /**
     * Weapon attached to the modification (assuming it's a weapon mount).
     */
    public ?Weapon $weapon = null;

    /**
     * List of all modifications.
     * @var array<mixed>
     */
    public static ?array $all_modifications;

    /**
     * Construct a new modification object.
     * @param array<string, array<int|string, string>|string> $options
     * @throws RuntimeException
     */
    public function __construct(public string $id, array $options = [])
    {
        $filename = config('shadowrun5e.data_path')
            . 'vehicle-modifications.php';
        self::$all_modifications ??= require $filename;

        $id = strtolower($id);
        if (!isset(self::$all_modifications[$id])) {
            throw new RuntimeException(
                sprintf('Vehicle modification "%s" is invalid', $id)
            );
        }

        $mod = self::$all_modifications[$id];
        $this->availability = $mod['availability'];
        $this->cost = $mod['cost'] ?? null;
        if (isset($mod['cost-attribute'])) {
            $this->costAttribute = $mod['cost-attribute'];
        }
        if (isset($mod['cost-multiplier'])) {
            $this->costMultiplier = (float)$mod['cost-multiplier'];
        }
        $this->description = $mod['description'];
        $this->effects = $mod['effects'] ?? [];
        $this->name = $mod['name'];
        $this->page = $mod['page'];
        $this->rating = $mod['rating'] ?? null;
        $this->requirements = $mod['requirements'] ?? [];
        $this->ruleset = $mod['ruleset'];
        $this->slotType = $mod['slot-type'] ?? null;
        $this->slots = $mod['slots'] ?? null;
        $this->type = $mod['type'] ?? null;

        // Vehicle modifications can be modified by other modifications.
        $this->modifications = new VehicleModificationArray();
        if (VehicleModificationType::VehicleModification == $this->type) {
            assert(null === $options['modifications'] || is_array($options['modifications']));
            foreach ($options['modifications'] ?? [] as $modMod) {
                $this->modifications[] = new VehicleModification($modMod);
            }
            if (isset($options['weapon'])) {
                assert(is_array($options['weapon']));
                $this->weapon = Weapon::buildWeapon($options['weapon']);
            }
        }
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * Return the cost of the modification.
     *
     * A vehicle mod can either have a plain cost or a cost that is based on the
     * vehicle it's being added to. For modifications that cost the same no
     * matter what kind of vehicle they're added to, just use the 'cost' field
     * and leave 'cost-attribute' and 'cost-multiplier' fields empty or unset.
     *
     * For modifications that depend on particular attributes of the vehicle
     * being modified, use the 'cost' and 'cost-attribute' fields. For example,
     * if the cost is listed as "Accel × 10,000¥", use 'cost' as 10000 and
     * 'cost-attribute' as 'acceleration'.
     *
     * For modifications that depend on the cost of the vehicle, such as
     * Off-road suspension (Rigger 5.0 p158) that cost "Vehicle cost × 25%",
     * leave the 'cost' and 'cost-attribute' fields unset and set
     * 'cost-multiplier' to 0.25.
     */
    public function getCost(Vehicle $vehicle): int
    {
        if (isset($this->costAttribute)) {
            return (int)$this->cost * (int)match ($this->costAttribute) {
                'acceleration' => $vehicle->stockAcceleration,
                'armor' => $vehicle->stockArmor, // @codeCoverageIgnore
                'body' => $vehicle->stockBody, // @codeCoverageIgnore
                'handling' => $vehicle->stockHandling, // @codeCoverageIgnore
                'handlingOffRoad' => $vehicle->stockHandlingOffRoad, // @codeCoverageIgnore
                'pilot' => $vehicle->stockPilot, // @codeCoverageIgnore
                'sensor' => $vehicle->stockSensor, // @codeCoverageIgnore
                'speed' => $vehicle->stockSpeed, // @codeCoverageIgnore
                default => 1,
            };
        }

        if (null !== $this->costMultiplier) {
            return (int)($vehicle->cost * $this->costMultiplier);
        }

        return (int)$this->cost;
    }

    /**
     * Get the number of slots this modification takes up, including any
     * additional modifications to it.
     */
    public function getSlots(): ?int
    {
        if (!isset($this->slots)) {
            return null;
        }

        $slots = $this->slots;
        foreach ($this->modifications as $mod) {
            $slots += $mod->slots ?? 0;
        }
        return $slots;
    }

    /**
     * If the modification has any requirements, test them on a vehicle to
     * determine whether they're allowed.
     */
    public function isAllowed(Vehicle $vehicle): bool
    {
        foreach ($this->requirements as $requirement) {
            if (!$requirement($vehicle)) {
                return false;
            }
        }
        return true;
    }
}
