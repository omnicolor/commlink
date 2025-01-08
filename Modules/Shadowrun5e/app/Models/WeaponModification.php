<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use Override;
use RuntimeException;
use Stringable;

use function config;
use function sprintf;
use function strtolower;

/**
 * Something to add to a character's weapon.
 */
final class WeaponModification implements Stringable
{
    public readonly string $availability;
    public int|null $cost;
    public int|null $costModifier;
    public readonly string $description;

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
    public readonly string $name;
    public readonly string $ruleset;
    public readonly string $type;

    /**
     * List of all modifications.
     * @var array<int, mixed>
     */
    public static ?array $modifications;

    /**
     * @throws RuntimeException
     */
    public function __construct(public readonly string $id)
    {
        $filename = config('shadowrun5e.data_path')
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

    #[Override]
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
        $filename = config('shadowrun5e.data_path')
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
