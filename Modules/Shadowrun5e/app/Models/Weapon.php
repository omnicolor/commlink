<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use Modules\Shadowrun5e\Enums\FiringMode;
use Modules\Shadowrun5e\Enums\WeaponClass;
use Modules\Shadowrun5e\Enums\WeaponRange;
use Override;
use RuntimeException;
use Stringable;

use function array_merge;
use function assert;
use function config;
use function sprintf;
use function str_contains;
use function str_replace;
use function strtolower;

/**
 * Weapon to take out the opposition.
 */
final class Weapon implements Stringable
{
    public WeaponModificationArray $accessories;
    public readonly string|null $accuracy;
    /** @var mixed[] */
    public array $ammunition = [];
    public readonly int|null $ammo_capacity;
    public readonly null|string $ammo_container;
    public readonly int|null $armor_piercing;
    public readonly string $availability;
    public WeaponClass $class;
    public readonly int|null $cost;
    public readonly string $damage;
    public readonly string $description;

    /**
     * Unique identifier for this instance of the weapon.
     */
    public ?string $link;

    /**
     * Identifier for the clip currently loaded.
     */
    public ?string $loaded;

    /** @var array<int, FiringMode> */
    public readonly array $modes;

    /**
     * Built-in modifications.
     */
    public WeaponModificationArray $modifications;

    /**
     * Added-on modifications.
     */
    public WeaponModificationArray $modifications_added;
    public readonly string $name;
    public readonly int|null $page;
    public readonly WeaponRange $range;
    public readonly int|null $reach;
    public readonly int|null $recoil_compensation;
    public readonly string $ruleset;
    public readonly ActiveSkill $skill;
    public readonly null|string $subname;

    /**
     * Type of combat for the weapon.
     */
    public string $type;

    /**
     * List of all weapons.
     * @var ?array<string, array<string, mixed>>
     */
    public static ?array $weapons;

    /**
     * @throws RuntimeException
     */
    public function __construct(public readonly string $id)
    {
        $filename = config('shadowrun5e.data_path') . 'weapons.php';
        self::$weapons ??= require $filename;

        $this->accessories = new WeaponModificationArray();
        $this->modifications = new WeaponModificationArray();

        if ('unarmed-strike' === $id) {
            $this->accuracy = 'physical';
            $this->ammo_capacity = null;
            $this->ammo_container = null;
            $this->armor_piercing = null;
            $this->availability = '';
            $this->cost = 0;
            $this->damage = '(STR)';
            $this->description = 'Unarmed Combat covers the various '
                . 'self-defense and attack moves that employ the body as a '
                . 'primary weapon. This includes a wide array of martial arts '
                . 'along with the use of cybernetic implant weaponry and the '
                . 'fighting styles that sprung up around those implants.';
            $this->modes = [];
            $this->name = 'Unarmed Strike';
            $this->page = 132;
            $this->range = WeaponRange::Melee;
            $this->reach = 0;
            $this->recoil_compensation = null;
            $this->ruleset = 'core';
            $this->skill = new ActiveSkill('unarmed-combat', 1);
            $this->subname = null;
            return;
        }

        $id = strtolower($id);
        if (!isset(self::$weapons[$id])) {
            throw new RuntimeException(sprintf(
                'Weapon ID "%s" is invalid',
                $id
            ));
        }

        $weapon = self::$weapons[$id];
        $this->accuracy = $weapon['accuracy'] ?? null;
        $this->ammo_capacity = $weapon['ammo-capacity'] ?? null;
        $this->ammo_container = $weapon['ammo-container'] ?? null;
        $this->armor_piercing = $weapon['armor-piercing'] ?? null;
        $this->availability = $weapon['availability'];
        $this->cost = $weapon['cost'];
        $this->description = $weapon['description'];
        $this->damage = $weapon['damage'];
        $this->modes = $weapon['modes'] ?? [];
        $this->name = $weapon['name'];
        $this->page = $weapon['page'] ?? null;
        $this->reach = $weapon['reach'] ?? null;
        $this->recoil_compensation = $weapon['recoil-compensation'] ?? null;
        $this->ruleset = $weapon['ruleset'] ?? 'core';
        $this->skill = new ActiveSkill($weapon['skill'], 1);
        $this->subname = $weapon['subname'] ?? null;
        $this->type = $weapon['type'];

        if (isset($weapon['modifications'])) {
            foreach ($weapon['modifications'] as $mod) {
                try {
                    $weaponMod = new WeaponModification($mod);
                } catch (RuntimeException) {
                    // Ignore modifications that aren't loadable
                    continue;
                }
                // Any modifications added here are built-in.
                $weaponMod->cost = null;
                $weaponMod->costModifier = null;
                $this->modifications[] = $weaponMod;
            }
        }

        if ($weapon['class'] instanceof WeaponClass) {
            $this->class = $weapon['class'];
        } else {
            $this->class = WeaponClass::from($weapon['class']);
        }

        // Depending on the state of the data files, the weapon's range could
        // be a variety of different things.
        if (isset($weapon['range']) && $weapon['range'] instanceof WeaponRange) {
            $this->range = $weapon['range'];
        } elseif (!isset($weapon['range'])) {
            $this->range = WeaponRange::tryFrom($this->class->value)
                ?? WeaponRange::Unknown; // @codeCoverageIgnore
        } else {
            $this->range = WeaponRange::tryFrom($weapon['range'])
                ?? WeaponRange::Unknown; // @codeCoverageIgnore
        }

        if (isset($weapon['mounts'])) {
            foreach ($weapon['mounts'] as $mount) {
                $this->accessories[$mount] = null;
            }
        }
    }

    #[Override]
    public function __toString(): string
    {
        return $this->name;
    }

    public static function all(): WeaponArray
    {
        $filename = config('shadowrun5e.data_path') . 'weapons.php';
        self::$weapons ??= require $filename;

        $weapons = new WeaponArray();
        foreach (array_keys(self::$weapons ?? []) as $id) {
            $weapons[] = new Weapon($id);
        }
        return $weapons;
    }

    /**
     * Build a weapon from a raw array.
     * @param array<mixed> $weapon
     */
    public static function buildWeapon(array $weapon): Weapon
    {
        $weaponObj = new Weapon($weapon['id']);
        foreach ($weapon['modifications'] ?? [] as $mod) {
            $weaponObj->modifications[] = new WeaponModification($mod);
        }
        foreach ($weapon['accessories'] ?? [] as $mount => $id) {
            $weaponObj->accessories[$mount] = new WeaponModification($id);
        }

        if (isset($weapon['ammo'])) {
            $filename = config('shadowrun5e.data_path') . 'ammunition.php';
            $ammoTypes = require $filename;
            foreach ($ammoTypes as $ammo) {
                $ammoTypes[$ammo['id']] = $ammo;
            }
            foreach ($weapon['ammo'] as $ammo) {
                $ammo = array_merge($ammoTypes[$ammo['id']], $ammo);
                $weaponObj->ammunition[] = $ammo;
            }
        }
        $weaponObj->link = $weapon['link'] ?? null;
        $weaponObj->loaded = $weapon['loaded'] ?? null;
        return $weaponObj;
    }

    /**
     * Return a weapon based on its name.
     * @throws RuntimeException
     */
    public static function findByName(string $name): Weapon
    {
        $filename = config('shadowrun5e.data_path') . 'weapons.php';
        self::$weapons ??= require $filename;

        foreach (self::$weapons ?? [] as $weapon) {
            if (strtolower((string)$weapon['name']) === strtolower($name)) {
                return new Weapon($weapon['id']);
            }
        }

        throw new RuntimeException(sprintf(
            'Weapon name "%s" was not found',
            $name
        ));
    }

    /**
     * Return the cost of the weapon, including ammo, modifications, and
     * accessories.
     */
    public function getCost(): int
    {
        $cost = (int)$this->cost;
        foreach ($this->modifications as $mod) {
            assert(null !== $mod);
            $cost += $mod->getCost($this);
        }
        foreach ($this->accessories as $mod) {
            // Accessories are might be null for a given slot.
            if (null === $mod) {
                continue;
            }
            $cost += $mod->getCost($this);
        }
        // TODO Add ammunition
        return $cost;
    }

    /**
     * Return the weapon's damage.
     */
    public function getDamage(int $strength): string
    {
        if (!str_contains($this->damage, 'STR')) {
            // Weapon is not strength-based.
            return $this->damage;
        }
        $damage = str_replace('(STR', '', $this->damage);
        $damage = (int)$damage + $strength;
        if ('unarmed-strike' === $this->id) {
            return $damage . 'S';
        }
        return $damage . 'P';
    }

    /**
     * @return array<int, string>
     */
    public function getModes(): array
    {
        $modes = [];
        foreach ($this->modes as $mode) {
            $modes[] = $mode->value;
        }
        return $modes;
    }

    /**
     * Get the range listing for a firearm.
     */
    public function getRange(): string
    {
        return $this->range->range();
    }
}
