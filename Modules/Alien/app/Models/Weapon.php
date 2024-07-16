<?php

declare(strict_types=1);

namespace Modules\Alien\Models;

use RuntimeException;
use Stringable;

class Weapon implements Stringable
{
    public const string RANGE_ENGAGED = 'engaged';
    public const string RANGE_SHORT = 'short';
    public const string RANGE_MEDIUM = 'medium';
    public const string RANGE_LONG = 'long';
    public const string RANGE_EXTREME = 'extreme';

    public const string CLASS_CLOSE_COMBAT = 'close-combat';
    public const string CLASS_HEAVY_WEAPON = 'heavy-weapon';
    public const string CLASS_PISTOL = 'pistol';
    public const string CLASS_RIFLE = 'rifle';

    public const string MODIFIER_ARMOR_DOUBLED = 'armor-doubled';
    public const string MODIFIER_ARMOR_PIERCING = 'armor-piercing';
    public const string MODIFIER_FIRE_INTENSITY_9 = 'fire-intensity-9';
    public const string MODIFIER_FULL_AUTO = 'full-auto';
    public const string MODIFIER_GRENADE_LAUNCHER = 'grenade-launcher';
    public const string MODIFIER_POWER_SUPPLY_5 = 'power-supply-5';
    public const string MODIFIER_SINGLE_SHOT = 'single-shot';
    public const string MODIFIER_STUN_EFFECT = 'stun-effect';
    public const string MODIFIER_STUN_EFFECT_2 = 'stun-effect-2';

    public int $bonus;
    public string $class;
    public int $cost;
    public ?int $damage;
    public string $description;
    /** @var array<int, string> */
    public array $modifiers;
    public string $name;
    public int $page;
    public string $range;
    public string $ruleset;
    public ?float $weight;

    /** @var array<string, array<int, string>|int|null|string> */
    public static array $weapons;

    public function __construct(public string $id)
    {
        $filename = config('alien.data_path') . 'weapons.php';
        /** @psalm-suppress UnresolvableInclude */
        self::$weapons ??= require $filename;

        $id = strtolower($id);
        if (!isset(self::$weapons[$id])) {
            throw new RuntimeException(sprintf(
                'Weapon ID "%s" is invalid',
                $id
            ));
        }

        $weapon = self::$weapons[$id];
        $this->bonus = $weapon['bonus'];
        $this->class = $weapon['class'];
        $this->cost = $weapon['cost'];
        $this->damage = $weapon['damage'];
        $this->description = $weapon['description'];
        $this->modifiers = $weapon['modifiers'];
        $this->name = $weapon['name'];
        $this->page = $weapon['page'];
        $this->range = $weapon['range'];
        $this->ruleset = $weapon['ruleset'];
        $this->weight = $weapon['weight'];
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @return array<int, self>
     */
    public static function all(): array
    {
        $filename = config('alien.data_path') . 'weapons.php';
        /** @psalm-suppress UnresolvableInclude */
        self::$weapons ??= require $filename;

        $weapons = [];
        /** @var string $id */
        foreach (array_keys(self::$weapons) as $id) {
            $weapons[] = new self($id);
        }
        return $weapons;
    }
}
