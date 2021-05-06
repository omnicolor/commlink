<?php

declare(strict_types=1);

namespace App\Models\CyberpunkRed;

/**
 * If something comes out of it, traverses a distance, and causes damage at the
 * end of that trajectory, it's a Ranged Weapon.
 */
class RangedWeapon
{
    public const QUALITY_POOR = 'poor';
    public const QUALITY_STANDARD = 'standard';
    public const QUALITY_EXCELLENT = 'excellent';

    public const QUALITIES = [
        self::QUALITY_POOR,
        self::QUALITY_STANDARD,
        self::QUALITY_EXCELLENT,
    ];

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
     * Single-shot damage from the weapon.
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
     * Number of rounds in the weapon's standard magazine.
     * @var int
     */
    public int $magazine;

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
     * List of all ranged weapons.
     * @var ?array<string, array<string, mixed>>
     */
    public static ?array $weapons;

    /**
     * Construct a new weapon.
     * @param array<string, string> $options
     * @throws \RuntimeException
     */
    public function __construct(array $options)
    {
        if (!isset($options['id'])) {
            throw new \RuntimeException(
                'ID must be included when instantiating a weapon'
            );
        }

        $filename = config('app.data_path.cyberpunkred') . 'ranged-weapons.php';
        self::$weapons ??= require $filename;

        $id = strtolower($options['id']);
        if (!array_key_exists($id, self::$weapons)) {
            throw new \RuntimeException(sprintf(
                'Weapon ID "%s" is invalid',
                $id
            ));
        }

        $weapon = self::$weapons[$id];
        $this->concealable = $weapon['concealable'];
        $this->cost = $weapon['cost'];
        $this->damage = $weapon['damage'];
        $this->examples = $weapon['examples'];
        $this->handsRequired = $weapon['hands-required'];
        $this->magazine = $weapon['magazine'];
        $this->rateOfFire = $weapon['rate-of-fire'];
        $this->skill = $weapon['skill'];
        $this->type = $weapon['type'];

        if (isset($options['quality'])) {
            if (!in_array($options['quality'], self::QUALITIES, true)) {
                throw new \RuntimeException(sprintf(
                    'Weapon ID "%s" has invalid quality "%s"',
                    $id,
                    $options['quality']
                ));
            }
            $this->quality = $options['quality'];
        }
        $this->name = $options['name'] ?? $this->type;
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
}
