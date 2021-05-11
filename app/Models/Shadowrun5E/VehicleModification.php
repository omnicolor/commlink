<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5E;

/**
 * Something to add to a vehicle.
 */
class VehicleModification
{
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
     * Description of the modification.
     * @var string
     */
    public string $description;

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
    public static ?array $modifications;

    /**
     * Construct a new modification object.
     * @param string $id ID to load
     * @throws \RuntimeException
     */
    public function __construct(string $id)
    {
        $filename = config('app.data_path.shadowrun5e')
            . 'vehicle-modifications.php';
        self::$modifications ??= require $filename;

        $id = \strtolower($id);
        if (!isset(self::$modifications[$id])) {
            throw new \RuntimeException(
                \sprintf('Vehicle modification "%s" is invalid', $id)
            );
        }

        $mod = self::$modifications[$id];
        $this->availability = $mod['availability'];
        $this->cost = $mod['cost'];
        $this->description = $mod['description'];
        $this->id = $id;
        $this->name = $mod['name'];
        $this->page = $mod['page'];
        $this->rating = $mod['rating'] ?? null;
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
}
