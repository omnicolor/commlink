<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5e;

/**
 * Something to add to a vehicle.
 */
class VehicleModification
{
    public const TYPE_EQUIPMENT = 'equipment';
    public const TYPE_VEHICLE_MOD = 'vehicle-mod';
    public const TYPE_MODIFICATION_MOD = 'modification-mod';

    /**
     * Availability code for the modification.
     * @var string
     */
    public string $availability;

    /**
     * Cost of the modification.
     * @var int
     */
    public int $cost;

    /**
     * Attribute of the vehicle to multiply the cost.
     * @var string
     */
    public string $costAttribute;

    /**
     * Description of the modification.
     * @var string
     */
    public string $description;

    /**
     * Collection of effects after installing the modification.
     * @var array<string, int>
     */
    public array $effects;

    /**
     * Unique identifier for the modification.
     * @var string
     */
    public string $id;

    /**
     * Name of the modification.
     * @var string
     */
    public string $name;

    /**
     * Page the modification is found on.
     * @var int
     */
    public int $page;

    /**
     * Rating of the modification.
     * @var ?int
     */
    public ?int $rating;

    /**
     * Requirements for the modification to be valid for a vehicle.
     * @var array<int, callable>
     */
    public array $requirements;

    /**
     * Ruleset identifier.
     * @var string
     */
    public string $ruleset;

    /**
     * For modifications, what type of slot it takes.
     * @var ?string
     */
    public ?string $slotType;

    /**
     * For modifications, how many slots it takes.
     * @var ?int
     */
    public ?int $slots;

    /**
     * List of all modifications.
     * @var array<mixed>
     */
    public static ?array $all_modifications;

    /**
     * Construct a new modification object.
     * @param string $id ID to load
     * @throws \RuntimeException
     */
    public function __construct(string $id)
    {
        $filename = config('app.data_path.shadowrun5e')
            . 'vehicle-modifications.php';
        self::$all_modifications ??= require $filename;

        $id = \strtolower($id);
        if (!isset(self::$all_modifications[$id])) {
            throw new \RuntimeException(
                \sprintf('Vehicle modification "%s" is invalid', $id)
            );
        }

        $mod = self::$all_modifications[$id];
        $this->availability = $mod['availability'];
        $this->cost = $mod['cost'];
        if (isset($mod['cost-attribute'])) {
            $this->costAttribute = $mod['cost-attribute'];
        }
        $this->description = $mod['description'];
        $this->effects = $mod['effects'] ?? [];
        $this->id = $id;
        $this->name = $mod['name'];
        $this->page = $mod['page'];
        $this->rating = $mod['rating'] ?? null;
        $this->requirements = $mod['requirements'] ?? [];
        $this->ruleset = $mod['ruleset'];
        $this->slotType = $mod['slot-type'] ?? null;
        $this->slots = $mod['slots'] ?? null;
    }

    /**
     * Return the name of the modification.
     * @return string
     */
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
     * and leave 'cost-attribute' empty or unset. For modifications that depend
     * on the vehicle being modified, use the 'cost' and 'cost-attribute'
     * fields. For example, if the cost is listed as "Accel × 10,000¥", use
     * 'cost' as 10000 and 'cost-attribute' as 'acceleration'.
     */
    public function getCost(Vehicle $vehicle): int
    {
        if (!isset($this->costAttribute)) {
            return $this->cost;
        }

        $attribute = 'stock' . ucfirst($this->costAttribute);
        /** @phpstan-ignore-next-line */
        return $vehicle->$attribute * $this->cost;
    }

    /**
     * If the modification has any requirements, test them on a vehicle to
     * determine whether they're allowed.
     * @psalm-suppress PossiblyUnusedMethod
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
