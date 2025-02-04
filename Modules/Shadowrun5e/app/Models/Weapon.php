<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

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
    /**
     * Collection of accessories.
     */
    public WeaponModificationArray $accessories;
    public readonly int|null|string $accuracy;

    /**
     * Array of ammunition.
     * @var mixed[]
     */
    public array $ammunition = [];

    /**
     * Number of rounds the weapon holds.
     */
    public readonly int|null $ammoCapacity;

    /**
     * Type of container for the ammunition.
     */
    public readonly null|string $ammoContainer;

    /**
     * Armor piercing base value for the weapon.
     */
    public readonly int|null $armorPiercing;
    public readonly string $availability;
    public readonly WeaponClass $class;
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

    /**
     * Modes the weapon can shoot in.
     * @var string[]
     */
    public ?array $modes;

    /**
     * Built-in modifications.
     */
    public WeaponModificationArray $modifications;

    /**
     * Added-on modifications.
     */
    public WeaponModificationArray $modificationsAdded;
    public readonly string $name;
    public readonly int|null $page;
    public readonly int|null $reach;
    public readonly int|null $recoilCompensation;
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
            $this->damage = '(STR)';
            $this->description = 'Unarmed Combat covers the various '
                . 'self-defense and attack moves that employ the body as a '
                . 'primary weapon. This includes a wide array of martial arts '
                . 'along with the use of cybernetic implant weaponry and the '
                . 'fighting styles that sprung up around those implants.';
            $this->name = 'Unarmed Strike';
            $this->reach = 0;
            $this->page = 132;
            $this->ruleset = 'core';
            $this->skill = new ActiveSkill('unarmed-combat');
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
        $this->ammoCapacity = $weapon['ammo-capacity'] ?? null;
        $this->ammoContainer = $weapon['ammo-container'] ?? null;
        $this->armorPiercing = $weapon['armor-piercing'] ?? null;
        $this->availability = $weapon['availability'];
        if ($weapon['class'] instanceof WeaponClass) {
            $this->class = $weaponClass;
        } else {
            $this->class = WeaponClass::from($weapon['class']);
        }

        $this->cost = $weapon['cost'];
        $this->description = $weapon['description'];
        $this->damage = $weapon['damage'];
        $this->modes = $weapon['modes'] ?? null;
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
        $this->name = $weapon['name'];
        $this->reach = $weapon['reach'] ?? null;
        $this->recoilCompensation = $weapon['recoil-compensation'] ?? null;
        $this->ruleset = $weapon['ruleset'] ?? 'core';
        $this->skill = $weapon['skill'];
        $this->subname = $weapon['subname'] ?? null;
        $this->type = $weapon['type'];
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
     * Get the range listing for a firearm.
     */
    public function getRange(): string
    {
        return match ($this->class) {
            WeaponClass::Cannon => '50/300/750/1200',
            WeaponClass::AssaultRifle => '25/150/350/550',
            WeaponClass::Bow => 'STR/STRx10/STRx30/STRx60',
            //'Grenade' => 'STRx2/STRx4/STRx6/STRx10',
            //'Grenade Launcher' => '5-50/100/150/500',
            WeaponClass::Crossbow => '14/45/120/180',
            WeaponClass::HeavyMachinegun => '40/250/750/1200',
            WeaponClass::HeavyPistol => '5/20/40/60',
            WeaponClass::HoldOutPistol => '5/15/30/50',
            //'Light Crossbow' => '6/24/60/120',
            //'Light Machinegun' => '25/200/400/800',
            WeaponClass::LightPistol => '5/15/30/50',
            WeaponClass::MachinePistol => '5/15/30/50',
            //'Medium Crossbow' => '9/36/90/150',
            //'Medium Machinegun' => '40/250/750/1200',
            WeaponClass::MissileLauncher => '20-70*/150/450/1500',
            WeaponClass::Shotgun => '10/40/80/150',
            //'Shotgun (flechette)' => '15/30/45/60',
            WeaponClass::SniperRifle => '50/350/800/1500',
            WeaponClass::SubmachineGun => '10/40/80/150',
            WeaponClass::Taser => '5/10/15/20',
            WeaponClass::ThrowingWeapon => 'STR/STRx2/STRx5/STRx7',
            //'Thrown Knife' => 'STR/STRx2/STRx3/STRx5',
            default => '???',
        };
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
}
