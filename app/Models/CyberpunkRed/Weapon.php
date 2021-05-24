<?php

declare(strict_types=1);

namespace App\Models\CyberpunkRed;

abstract class Weapon
{
    public const QUALITY_POOR = 'poor';
    public const QUALITY_STANDARD = 'standard';
    public const QUALITY_EXCELLENT = 'excellent';

    public const QUALITIES = [
        self::QUALITY_POOR,
        self::QUALITY_STANDARD,
        self::QUALITY_EXCELLENT,
    ];

    public const TYPE_MELEE = 'melee';
    public const TYPE_RANGED = 'ranged';

    /**
     * Whether the weapon can be concealed.
     * @var bool
     */
    public bool $concealable;

    /**
     * How many eddies you have to spend for the weapon at standard quality.
     * @var int
     */
    public int $cost;

    /**
     * Single-shot/hit damage from the weapon.
     * @var string
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
     * @var int
     */
    public int $handsRequired;

    /**
     * Name of the weapon. Defaults to the weapon's type.
     * @var string
     */
    public string $name;

    /**
     * Weapon's build quality.
     * @var string
     */
    public string $quality = self::QUALITY_STANDARD;

    /**
     * The weapon's rate of fire, how many shots you can take in a combat turn.
     * @var int
     */
    public int $rateOfFire;

    /**
     * ID of the skill used to fire the weapon.
     * @var string
     */
    public string $skill;

    /**
     * Type of weapon (Medium pistol, Crossbow, etc).
     * @var string
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

    /**
     * Return the name of the weapon.
     * @return string
     */
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * Return the cost of the weapon, including quality modifier and
     * accessories.
     * @return int
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
     * @return Weapon
     * @throws \RuntimeException
     */
    public static function build(array $options): Weapon
    {
        if (null === self::$rangedWeapons) {
            $filename = config('app.data_path.cyberpunkred')
                . 'ranged-weapons.php';
            self::$rangedWeapons = require $filename;
        }

        if (null === self::$meleeWeapons) {
            $filename = config('app.data_path.cyberpunkred')
                . 'melee-weapons.php';
            self::$meleeWeapons = require $filename;
        }

        if (!isset($options['id'])) {
            throw new \RuntimeException(
                'ID must be included when instantiating a weapon'
            );
        }

        $id = \strtolower((string)$options['id']);
        if (\array_key_exists($id, self::$rangedWeapons)) {
            return new RangedWeapon($options);
        }

        if (\array_key_exists($id, self::$meleeWeapons)) {
            return new MeleeWeapon($options);
        }
        throw new \RuntimeException(\sprintf('Weapon ID "%s" is invalid', $id));
    }
}
