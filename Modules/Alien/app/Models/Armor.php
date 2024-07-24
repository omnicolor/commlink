<?php

declare(strict_types=1);

namespace Modules\Alien\Models;

use RuntimeException;
use Stringable;

use function config;
use function sprintf;
use function strtolower;

class Armor implements Stringable
{
    public const string MODIFIER_AGILITY_DECREASE = 'agility-decrease';
    public const string MODIFIER_CLOSE_COMBAT_INCREASE = 'close-combat-increase';
    public const string MODIFIER_COMM_UNIT = 'comm-unit';
    public const string MODIFIER_HEAVY_MACHINERY_INCREASE = 'heavy-machinery-increase';
    public const string MODIFIER_SURVIVAL_INCREASE = 'survival-increase';

    public int $air_supply;
    public int $cost;
    public string $description;
    /** @var array<int, string> */
    public array $modifiers;
    public string $name;
    public int $page;
    public int $rating;
    public string $ruleset;
    public ?float $weight;

    /** @var array<string, array<int, string>|int|null|string> */
    public static array $armor;

    public function __construct(public string $id)
    {
        $filename = config('alien.data_path') . 'armor.php';
        /** @psalm-suppress UnresolvableInclude */
        self::$armor ??= require $filename;

        $id = strtolower($id);
        if (!isset(self::$armor[$id])) {
            throw new RuntimeException(sprintf(
                'Armor ID "%s" is invalid',
                $id
            ));
        }

        $armor = self::$armor[$id];
        $this->air_supply = $armor['air_supply'];
        $this->cost = $armor['cost'];
        $this->description = $armor['description'];
        $this->modifiers = $armor['modifiers'];
        $this->name = $armor['name'];
        $this->page = $armor['page'];
        $this->rating = $armor['rating'];
        $this->ruleset = $armor['ruleset'];
        $this->weight = $armor['weight'];
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
        $filename = config('alien.data_path') . 'armor.php';
        /** @psalm-suppress UnresolvableInclude */
        self::$armor ??= require $filename;

        $armor = [];
        /** @var string $id */
        foreach (array_keys(self::$armor) as $id) {
            $armor[] = new self($id);
        }
        return $armor;
    }
}
