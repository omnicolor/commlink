<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use RuntimeException;
use Stringable;

use function config;
use function sprintf;
use function strtolower;

/**
 * Something to add to an item.
 * @psalm-suppress PossiblyUnusedProperty
 */
class GearModification implements Stringable
{
    /**
     * Availability of the modification.
     */
    public string $availability;

    /**
     * Amount of capacity in the modified gear taken up.
     */
    public ?int $capacityCost;

    /**
     * What kind of container the modification can go in.
     */
    public string $containerType;

    /**
     * Cost of the modification.
     */
    public int $cost;

    /**
     * Description of the modification.
     */
    public string $description;

    /**
     * List of effects the modification has in game terms.
     * @var array<string, int>
     */
    public array $effects;

    /**
     * Name of the modification.
     */
    public string $name;

    /**
     * Page number.
     */
    public ?int $page;

    /**
     * Rating of the modification.
     */
    public ?int $rating;

    /**
     * Ruleset code for where the modification was added.
     */
    public string $ruleset;

    /**
     * List of effects the modification has when wireless is on.
     * @var array<string, int>
     */
    public array $wirelessEffects;

    /**
     * List of all modifications.
     * @var ?array<string, array<string, mixed>>
     */
    public static ?array $modifications;

    /**
     * Construct a new Gear Modification object.
     * @throws RuntimeException
     */
    public function __construct(public string $id)
    {
        $filename = config('shadowrun5e.data_path')
            . 'gear-modifications.php';
        /** @psalm-suppress UnresolvableInclude */
        self::$modifications ??= require $filename;

        $id = strtolower($id);
        if (!isset(self::$modifications[$id])) {
            throw new RuntimeException(sprintf(
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
        $this->name = $mod['name'];
        $this->page = $mod['page'] ?? null;
        $this->rating = $mod['rating'] ?? null;
        $this->ruleset = $mod['ruleset'] ?? 'core';
        $this->wirelessEffects = $mod['wireless-effects'] ?? [];
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getCost(): int
    {
        return $this->cost;
    }
}
