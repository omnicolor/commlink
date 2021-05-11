<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5E;

/**
 * Something to change a piece of armor's behavior.
 */
class ArmorModification
{
    /**
     * Availability code for the modification.
     * @var string
     */
    public ?string $availability;

    /**
     * Cost of the modification.
     * @var int
     */
    public int $cost;

    /**
     * Cost modifier for the modification.
     * @var float
     */
    public float $costModifier;

    /**
     * Description of the modification.
     * @var string
     */
    public string $description;

    /**
     * List of effects for the modification.
     * @var array<string, int>
     */
    public array $effects = [];

    /**
     * ID of the modification.
     * @var string
     */
    public string $id;

    /**
     * List of modifications this is incompatible with.
     * @var string[]
     */
    public array $incompatibleWith = [];

    /**
     * Name of the modification.
     * @var string
     */
    public string $name;

    /**
     * Rating for the modification.
     * @var int
     */
    public ?int $rating;

    /**
     * Ruleset the modification comes from.
     * @var string
     */
    public string $ruleset = 'core';

    /**
     * List of all modifications.
     * @var ?array<mixed>
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
            . 'armor-modifications.php';
        self::$modifications ??= require $filename;

        $id = \strtolower($id);
        if (!isset(self::$modifications[$id])) {
            throw new \RuntimeException(\sprintf(
                'Modification ID "%s" not found',
                $id
            ));
        }

        $mod = self::$modifications[$id];
        $this->availability = $mod['availability'];
        if (isset($mod['cost'])) {
            $this->cost = $mod['cost'];
        } else {
            $this->cost = 0;
            $this->costModifier = $mod['cost-multiplier'];
        }
        $this->description = $mod['description'];
        $this->effects = $mod['effects'] ?? [];
        $this->id = $mod['id'];
        $this->name = $mod['name'];
        $this->rating = $mod['rating'] ?? null;
        $this->ruleset = $mod['ruleset'] ?? 'core';
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
     * @param mixed $armor
     * @return int
     */
    public function getCost($armor): int
    {
        if (0 !== $this->cost) {
            return $this->cost;
        }
        return (int)(($armor->cost * $this->costModifier) - $armor->cost);
    }
}
