<?php

declare(strict_types=1);

namespace Modules\Cyberpunkred\Models;

use Override;
use RuntimeException;
use Stringable;

use function array_key_exists;
use function array_keys;
use function config;
use function sprintf;
use function strtolower;

abstract class Weapon implements Stringable
{
    public const string QUALITY_POOR = 'poor';
    public const string QUALITY_STANDARD = 'standard';
    public const string QUALITY_EXCELLENT = 'excellent';

    public const array QUALITIES = [
        self::QUALITY_POOR,
        self::QUALITY_STANDARD,
        self::QUALITY_EXCELLENT,
    ];

    public const string TYPE_MELEE = 'melee';
    public const string TYPE_RANGED = 'ranged';

    /**
     * Whether the weapon can be concealed.
     */
    public bool $concealable;

    /**
     * How many eddies you have to spend for the weapon at standard quality.
     */
    public int $cost;

    /**
     * Single-shot/hit damage from the weapon.
     */
    public string $damage;

    /**
     * Examples of the different qualities of weapon.
     * For example: [
     *     'poor' => ['Example A'],
     *     'standard' => ['Example 1'],
     *     'excellent' => ['Example 🔫'],
     * ].
     * @var array<string, array<int, string>>
     */
    public array $examples = [
        self::QUALITY_POOR => [],
        self::QUALITY_STANDARD => [],
        self::QUALITY_EXCELLENT => [],
    ];

    /**
     * Number of hands required to wield the weapon.
     */
    public int $handsRequired;

    public string $id;

    /**
     * Name of the weapon. Defaults to the weapon's type.
     */
    public string $name;

    /**
     * Weapon's build quality.
     */
    public string $quality = self::QUALITY_STANDARD;

    /**
     * The weapon's rate of fire, how many shots you can take in a combat turn.
     */
    public int $rateOfFire;

    /**
     * ID of the skill used to fire the weapon.
     */
    public string $skill;

    /**
     * Type of weapon (Medium pistol, Crossbow, etc).
     */
    public string $type;

    /**
     * Collection of all ranged weapons.
     * @var array<string, array<string, mixed>>
     */
    public static ?array $rangedWeapons = null;

    /**
     * Collection of all melee weapons.
     * @var array<string, array<string, mixed>>
     */
    public static ?array $meleeWeapons = null;

    #[Override]
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * Return the cost of the weapon, including quality modifier and
     * accessories.
     */
    public function getCost(): int
    {
        // Weapons have a base cost but that changes with a poor or excellent
        // quality version of the weapon.
        $costs = [
            50 => [
                self::QUALITY_POOR => 20,
                self::QUALITY_STANDARD => 50,
                self::QUALITY_EXCELLENT => 100,
            ],
            100 => [
                self::QUALITY_POOR => 50,
                self::QUALITY_STANDARD => 100,
                self::QUALITY_EXCELLENT => 500,
            ],
            500 => [
                self::QUALITY_POOR => 100,
                self::QUALITY_STANDARD => 500,
                self::QUALITY_EXCELLENT => 1000,
            ],
        ];
        return $costs[$this->cost][$this->quality];
    }

    /**
     * Build a new Weapon object from the datastore's array.
     * @param array<string, string|int> $options
     * @throws RuntimeException
     */
    public static function build(array $options): Weapon
    {
        if (null === self::$rangedWeapons) {
            $filename = config('cyberpunkred.data_path') . 'ranged-weapons.php';
            self::$rangedWeapons = require $filename;
        }

        if (null === self::$meleeWeapons) {
            $filename = config('cyberpunkred.data_path') . 'melee-weapons.php';
            self::$meleeWeapons = require $filename;
        }

        if (!isset($options['id'])) {
            throw new RuntimeException(
                'ID must be included when instantiating a weapon'
            );
        }

        $id = strtolower((string)$options['id']);
        if (array_key_exists($id, self::$rangedWeapons ?? [])) {
            return new RangedWeapon($options);
        }

        if (array_key_exists($id, self::$meleeWeapons ?? [])) {
            return new MeleeWeapon($options);
        }
        throw new RuntimeException(sprintf('Weapon ID "%s" is invalid', $id));
    }

    public static function findByName(string $name): Weapon
    {
        $filename = config('cyberpunkred.data_path') . 'ranged-weapons.php';
        self::$rangedWeapons = require $filename;

        $filename = config('cyberpunkred.data_path') . 'melee-weapons.php';
        self::$meleeWeapons = require $filename;

        $lowerName = strtolower($name);
        foreach (self::$rangedWeapons ?? [] as $id => $weapon) {
            if ($lowerName === strtolower($weapon['type'])) {
                return new RangedWeapon(['id' => $id]);
            }
        }
        foreach (self::$meleeWeapons ?? [] as $id => $weapon) {
            if ($lowerName === strtolower($weapon['type'])) {
                return new MeleeWeapon(['id' => $id]);
            }
        }

        throw new RuntimeException(sprintf('Weapon "%s" was not found', $name));
    }

    /**
     * @return array<int, self>
     */
    public static function all(): array
    {
        $filename = config('cyberpunkred.data_path') . 'ranged-weapons.php';
        self::$rangedWeapons = require $filename;

        $filename = config('cyberpunkred.data_path') . 'melee-weapons.php';
        self::$meleeWeapons = require $filename;

        $weapons = [];
        /** @var string $id */
        /** @var string $id */
        foreach (array_keys(self::$meleeWeapons ?? []) as $id) {
            $weapons[] = new MeleeWeapon(['id' => $id]);
        }
        foreach (array_keys(self::$rangedWeapons ?? []) as $id) {
            $weapons[] = new RangedWeapon(['id' => $id]);
        }
        return $weapons;
    }
}
