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

    /*
     * $cost or $costModifier should be set for an aftermarket modification,
     * but not both. For built-in modifications, the Weapon constructor will
     * null out both so they don't add to the cost of the weapon.
     */
    public int|null $cost;
    public int|null $costModifier;

    public readonly string $description;
    /** @var array<string, int> */
    public array $effects;
    /** @var array<string, int> */
    public readonly array $wirelessEffects;

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
    public readonly int|null $page;
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
        if (isset($mod['cost'])) {
            $this->cost = (int)$mod['cost'];
            $this->costModifier = null;
        } else {
            $this->cost = null;
            $this->costModifier = $mod['cost-modifier'];
        }
        $this->description = $mod['description'];
        $this->effects = $mod['effects'] ?? [];
        $this->incompatibleWith = $mod['incompatible-with'] ?? [];
        $this->mount = $mod['mount'] ?? [];
        $this->name = $mod['name'];
        $this->page = $mod['page'] ?? null;
        $this->ruleset = $mod['ruleset'] ?? 'core';
        $this->type = $mod['type'];
        $this->wirelessEffects = $mod['wireless-effects'] ?? [];
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

        foreach (self::$modifications ?? [] as $mod) {
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
