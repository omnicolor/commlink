<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5e;

/**
 * Something to add to an item.
 */
class GearModification
{
    /**
     * Availability of the modification.
     * @var string
     */
    public string $availability;

    /**
     * Amount of capacity in the modified gear taken up.
     * @var int
     */
    public ?int $capacityCost;

    /**
     * What kind of container the modification can go in.
     * @var string
     */
    public string $containerType;

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
     * List of effects the modification has in game terms.
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
     * Page number.
     * @var int
     */
    public ?int $page;

    /**
     * Rating of the modification.
     * @var int
     */
    public ?int $rating;

    /**
     * Ruleset code for where the modification was added.
     * @var string
     */
    public string $ruleset;

    /**
     * List of effects the modification has when wireless is on.
     * @var array<string, int>
     */
    public array $wirelessEffects;

    /**
     * List of all modifications.
     * @var ?array<mixed>
     */
    public static ?array $modifications;

    /**
     * Construct a new Gear Modification object.
     * @param string $id
     * @throws \RuntimeException
     */
    public function __construct(string $id)
    {
        $filename = config('app.data_path.shadowrun5e')
            . 'gear-modifications.php';
        self::$modifications ??= require $filename;

        $id = \strtolower($id);
        if (!isset(self::$modifications[$id])) {
            throw new \RuntimeException(\sprintf(
                'Gear mod "%s" not found',
                $id
            ));
        }

        $mod = self::$modifications[$id];

        $this->availability = $mod['availability'];
        $this->capacityCost = $mod['capacity-cost'] ?? 0;
        $this->containerType = $mod['container-type'];
        $this->cost = $mod['cost'];
        $this->description = $mod['description'];
        $this->effects = $mod['effects'] ?? [];
        $this->id = $id;
        $this->name = $mod['name'];
        $this->page = $mod['page'] ?? null;
        $this->rating = $mod['rating'] ?? null;
        $this->ruleset = $mod['ruleset'] ?? 'core';
        $this->wirelessEffects = $mod['wireless-effects'] ?? [];
    }

    /**
     * Return the modification's name.
     * @return string
     */
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * Return the cost for this modification.
     * @return int
     */
    public function getCost(): int
    {
        return $this->cost;
    }
}
