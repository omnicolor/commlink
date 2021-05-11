<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5E;

/**
 * Something to add to a character's weapon.
 */
class WeaponModification
{
    /**
     * Availability code for the modification.
     * @var string
     */
    public string $availability;

    /**
     * Cost of the modification.
     * @var ?int
     */
    public ?int $cost;

    /**
     * Cost modifier for the modification.
     * @var ?int
     */
    public ?int $costModifier;

    /**
     * Description of the modification.
     * @var string
     */
    public string $description;

    /**
     * List of effects for the modification.
     * @var array<string, int>
     */
    public array $effects;

    /**
     * ID of the modification.
     * @var string
     */
    public string $id;

    /**
     * List of modifications this is incompatible with.
     * @var string[]
     */
    public array $incompatibleWith;

    /**
     * List of locations the modification can be installed on.
     * @var string[]
     */
    public array $mount;

    /**
     * Name of the modification.
     * @var string
     */
    public string $name;

    /**
     * Ruleset the modification comes from.
     * @var string
     */
    public string $ruleset;

    /**
     * Type of modification (accessory or modification).
     * @var string
     */
    public string $type;

    /**
     * List of all modifications.
     * @var array<int, mixed>
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
            . 'weapon-modifications.php';
        self::$modifications ??= require $filename;

        if (!isset(self::$modifications[$id])) {
            throw new \RuntimeException(\sprintf(
                'Modification ID "%s" is invalid',
                $id
            ));
        }
        $mod = self::$modifications[$id];
        $this->availability = $mod['availability'];
        $this->cost = $mod['cost'] ?? null;
        $this->costModifier = $mod['cost-modifier'] ?? null;
        $this->description = $mod['description'];
        $this->effects = $mod['effects'] ?? [];
        $this->id = $mod['id'];
        $this->incompatibleWith = $mod['incompatible-with'] ?? [];
        $this->mount = $mod['mount'] ?? [];
        $this->name = $mod['name'];
        $this->ruleset = $mod['ruleset'] ?? 'core';
        $this->type = $mod['type'];
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
     * @param Weapon $weapon
     * @return int
     */
    public function getCost(Weapon $weapon): int
    {
        if (isset($this->costModifier)) {
            return ($this->costModifier - 1) * (int)$weapon->cost;
        }
        return $this->cost ?? 0;
    }
}
