<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5e;

use RuntimeException;
use Stringable;

use function config;
use function sprintf;
use function strtolower;

/**
 * Something to add to a character's weapon.
 * @psalm-suppress PossiblyUnusedProperty
 */
class WeaponModification implements Stringable
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
     * Cost modifier for the modification.
     */
    public ?int $costModifier;

    /**
     * Description of the modification.
     */
    public string $description;

    /**
     * List of effects for the modification.
     * @var array<string, int>
     */
    public array $effects;

    /**
     * List of modifications this is incompatible with.
     * @var array<int, string>
     */
    public array $incompatibleWith;

    /**
     * List of locations the modification can be installed on.
     * @var array<int, string>
     */
    public array $mount;

    /**
     * Name of the modification.
     */
    public string $name;

    /**
     * Ruleset the modification comes from.
     */
    public string $ruleset;

    /**
     * Type of modification (accessory or modification).
     */
    public string $type;

    /**
     * List of all modifications.
     * @var array<int, mixed>
     */
    public static ?array $modifications;

    /**
     * @throws RuntimeException
     */
    public function __construct(public string $id)
    {
        $filename = config('app.data_path.shadowrun5e')
            . 'weapon-modifications.php';
        self::$modifications ??= require $filename;

        $id = strtolower($id);
        if (!isset(self::$modifications[$id])) {
            throw new RuntimeException(sprintf(
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
        $this->incompatibleWith = $mod['incompatible-with'] ?? [];
        $this->mount = $mod['mount'] ?? [];
        $this->name = $mod['name'];
        $this->ruleset = $mod['ruleset'] ?? 'core';
        $this->type = $mod['type'];
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getCost(Weapon $weapon): int
    {
        if (isset($this->costModifier)) {
            return ($this->costModifier - 1) * (int)$weapon->cost;
        }
        return $this->cost ?? 0;
    }

    /**
     * Find a weapon modification by its name.
     * @throws RuntimeException
     */
    public static function findByName(string $name): WeaponModification
    {
        $filename = config('app.data_path.shadowrun5e')
            . 'weapon-modifications.php';
        self::$modifications ??= require $filename;

        foreach (self::$modifications as $mod) {
            if (strtolower((string)$mod['name']) === strtolower($name)) {
                return new WeaponModification($mod['id']);
            }
        }

        throw new RuntimeException(sprintf(
            'Weapon modification "%s" was not found',
            $name
        ));
    }
}
