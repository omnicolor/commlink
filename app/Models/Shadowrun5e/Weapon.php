<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5e;

/**
 * Weapon to take out the opposition.
 */
class Weapon
{
    /**
     * Collection of accessories.
     * @var WeaponModificationArray
     */
    public WeaponModificationArray $accessories;

    /**
     * Accuracy of the weapon.
     * @var string|int|null
     */
    public int | null | string $accuracy;

    /**
     * Array of ammunition.
     * @var mixed[]
     */
    public array $ammunition = [];

    /**
     * Number of rounds the weapon holds.
     * @var ?int
     */
    public ?int $ammoCapacity;

    /**
     * Type of container for the ammunition.
     * @var string
     */
    public ?string $ammoContainer;

    /**
     * Armor piercing base value for the weapon.
     * @var ?int
     */
    public ?int $armorPiercing;

    /**
     * Availability code for the weapon.
     * @var string
     */
    public string $availability = '';

    /**
     * Class of the weapon.
     * @var string
     */
    public string $class;

    /**
     * Cost of the weapon.
     * @var ?int
     */
    public ?int $cost;

    /**
     * Damage code for the weapon.
     * @var string
     */
    public string $damage;

    /**
     * Description of the weapon.
     * @var string
     */
    public string $description;

    /**
     * Unique identifier for the weapon's information.
     * @var string
     */
    public string $id;

    /**
     * Unique identifier for this instance of the weapon.
     * @var ?string
     */
    public ?string $link;

    /**
     * Identifier for the clip currently loaded.
     * @var ?string
     */
    public ?string $loaded;

    /**
     * Modes the weapon can shoot in.
     * @var string[]
     */
    public ?array $modes;

    /**
     * Built-in modifications.
     * @var WeaponModificationArray
     */
    public WeaponModificationArray $modifications;

    /**
     * Added-on modifications.
     * @var WeaponModificationArray
     */
    public WeaponModificationArray $modificationsAdded;

    /**
     * Name of the weapon.
     * @var string
     */
    public string $name;

    /**
     * Page the weapon was added on.
     * @var ?int
     */
    public ?int $page;

    /**
     * Weapon's reach.
     * @var ?int
     */
    public ?int $reach;

    /**
     * Recoil compensation.
     * @var ?int
     */
    public ?int $recoilCompensation;

    /**
     * Ruleset the weapon is listed in.
     * @var string
     */
    public string $ruleset = 'core';

    /**
     * Skill to use for the weapon.
     * @var string
     */
    public string $skill;

    /**
     * Subname for the weapon.
     * @var ?string
     */
    public ?string $subname;

    /**
     * Type of combat for the weapon.
     * @var string
     */
    public string $type;

    /**
     * List of all weapons.
     * @var ?array<int, mixed>
     */
    public static ?array $weapons;

    /**
     * Construct a new weapon object.
     * @param string $id ID to load
     * @throws \RuntimeException
     */
    public function __construct(string $id)
    {
        $filename = config('app.data_path.shadowrun5e') . 'weapons.php';
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
            $this->id = 'unarmed-strike';
            $this->name = 'Unarmed Strike';
            $this->reach = 0;
            $this->page = 132;
            $this->ruleset = 'core';
            $this->skill = 'unarmed-combat';
            return;
        }

        if (!\array_key_exists($id, self::$weapons)) {
            throw new \RuntimeException(\sprintf(
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
        $this->class = $weapon['class'] ?? null;
        $this->cost = $weapon['cost'];
        $this->description = $weapon['description'];
        $this->damage = $weapon['damage'];
        $this->id = $weapon['id'];
        $this->modes = $weapon['modes'] ?? null;
        if (isset($weapon['modifications'])) {
            foreach ($weapon['modifications'] as $mod) {
                try {
                    $weaponMod = new WeaponModification($mod);
                } catch (\RuntimeException $e) {
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

    /**
     * Return the name of the weapon.
     * @return string
     */
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * Return the cost of the weapon, including ammo, modifications, and
     * accessories.
     * @return int
     */
    public function getCost(): int
    {
        $cost = (int)$this->cost;
        foreach ($this->modifications as $mod) {
            // Modifications are guaranteed to not be null.
            // @phpstan-ignore-next-line
            $cost += $mod->getCost($this);
        }
        foreach ($this->accessories as $slot => $mod) {
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
     * @param int $strength Character's strength
     */
    public function getDamage(int $strength): string
    {
        if (false === \strpos($this->damage, 'STR')) {
            // Weapon is not strength-based.
            return $this->damage;
        }
        $damage = \str_replace('(STR', '', $this->damage);
        $damage = (int)$damage + $strength;
        if ('unarmed-strike' === $this->id) {
            return $damage . 'S';
        }
        return $damage . 'P';
    }

    /**
     * Get the range listing for a firearm.
     * @return string
     */
    public function getRange(): string
    {
        switch ($this->class) {
            case 'Assault Cannon':
                return '50/300/750/1200';
            case 'Assault Rifle':
                return '25/150/350/550';
            case 'Bow':
                return 'STR/STRx10/STRx30/STRx60';
            case 'Grenade':
                return 'STRx2/STRx4/STRx6/STRx10';
            case 'Grenade Launcher':
                return '5-50/100/150/500';
            case 'Heavy Crossbow':
                return '14/45/120/180';
            case 'Heavy Machinegun':
                return '40/250/750/1200';
            case 'Heavy Pistol':
                return '5/20/40/60';
            case 'Hold-Out Pistol':
                return '5/15/30/50';
            case 'Light Crossbow':
                return '6/24/60/120';
            case 'Light Machinegun':
                return '25/200/400/800';
            case 'Light Pistol':
                return '5/15/30/50';
            case 'Machine Pistol':
                return '5/15/30/50';
            case 'Medium Crossbow':
                return '9/36/90/150';
            case 'Medium Machinegun':
                return '40/250/750/1200';
            case 'Missile Launcher':
                return '20-70*/150/450/1500';
            case 'Shotgun':
                return '10/40/80/150';
            case 'Shotgun (flechette)':
                return '15/30/45/60';
            case 'Sniper Rifle':
                return '50/350/800/1500';
            case 'Submachine Gun':
                return '10/40/80/150';
            case 'Taser':
                return '5/10/15/20';
            case 'Throwing Weapon':
                return 'STR/STRx2/STRx5/STRx7';
            case 'Thrown Knife':
                return 'STR/STRx2/STRx3/STRx5';
            default:
                return '???';
        }
    }

    /**
     * Build a weapon from a raw array.
     * @param array<mixed> $weapon
     * @return Weapon
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
            $filename = config('app.data_path.shadowrun5e') . 'ammunition.php';
            $ammoTypes = require $filename;
            foreach ($ammoTypes as $ammo) {
                $ammoTypes[$ammo['id']] = $ammo;
            }
            foreach ($weapon['ammo'] as $ammo) {
                $ammo = \array_merge($ammoTypes[$ammo['id']], $ammo);
                $weaponObj->ammunition[] = $ammo;
            }
        }
        $weaponObj->link = $weapon['link'] ?? null;
        $weaponObj->loaded = $weapon['loaded'] ?? null;
        return $weaponObj;
    }

    /**
     * Return a weapon based on its name.
     * @param string $name
     * @return Weapon
     * @throws \RuntimeException
     */
    public static function findByName(string $name): Weapon
    {
        $filename = config('app.data_path.shadowrun5e') . 'weapons.php';
        self::$weapons ??= require $filename;

        foreach (self::$weapons as $weapon) {
            if (\strtolower($weapon['name']) === \strtolower($name)) {
                return new Weapon($weapon['id']);
            }
        }

        throw new \RuntimeException(\sprintf(
            'Weapon name "%s" was not found',
            $name
        ));
    }
}
