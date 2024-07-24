<?php

declare(strict_types=1);

namespace Modules\Alien\Models;

use RuntimeException;
use Stringable;

use function config;
use function sprintf;
use function strtolower;

/**
 * @psalm-suppress PossiblyUnusedProperty
 */
class Gear implements Stringable
{
    public const string CATEGORY_COMMUNICATIONS = 'communications';
    public const string CATEGORY_DATA_STORAGE = 'data-storage';
    public const string CATEGORY_DIAGNOSTICS = 'diagnostics';
    public const string CATEGORY_FOOD_AND_DRINK = 'food-and-drink';
    public const string CATEGORY_MAINFRAMES = 'mainframes';
    public const string CATEGORY_MEDICAL = 'medical';
    public const string CATEGORY_MISCELLANEOUS = 'miscellaneous';
    public const string CATEGORY_PHARMACEUTICALS = 'pharmaceuticals';
    public const string CATEGORY_TOOLS = 'tools';
    public const string CATEGORY_VISION = 'vision';

    public string $category;
    public ?int $cost;
    public string $description;
    /** @var array<string, int> */
    public array $effects;
    public string $effects_text;
    public string $name;
    public int $page;
    public int $quantity;
    public string $ruleset;
    public ?float $weight;

    /** @var array<string, array<int, string>|int|null|string> */
    public static array $gear;

    public function __construct(public string $id, ?int $quantity = 1)
    {
        $filename = config('alien.data_path') . 'gear.php';
        /** @psalm-suppress UnresolvableInclude */
        self::$gear ??= require $filename;

        $id = strtolower($id);
        if (!isset(self::$gear[$id])) {
            throw new RuntimeException(sprintf('Gear ID "%s" is invalid', $id));
        }

        $gear = self::$gear[$id];
        $this->category = $gear['category'];
        $this->cost = $gear['cost'];
        $this->description = $gear['description'];
        $this->effects = $gear['effects'];
        $this->effects_text = $gear['effects_text'];
        $this->name = $gear['name'];
        $this->page = $gear['page'];
        $this->ruleset = $gear['ruleset'];
        $this->weight = $gear['weight'];

        if (null === $quantity) {
            $this->quantity = 1;
        } else {
            $this->quantity = $quantity;
        }
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @return array<int, Gear>
     */
    public static function all(): array
    {
        $filename = config('alien.data_path') . 'gear.php';
        /** @psalm-suppress UnresolvableInclude */
        self::$gear ??= require $filename;

        $gear = [];
        /** @var string $id */
        foreach (array_keys(self::$gear) as $id) {
            $gear[] = new self($id);
        }
        return $gear;
    }
}
