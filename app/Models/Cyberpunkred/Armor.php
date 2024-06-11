<?php

declare(strict_types=1);

namespace App\Models\Cyberpunkred;

use RuntimeException;
use Stringable;

use function config;
use function sprintf;
use function strtolower;

/**
 * @psalm-suppress UnusedClass
 */
class Armor implements Stringable
{
    public CostCategory $cost_category;
    public string $description;
    public int $page;
    public int $penalty;
    public string $ruleset;
    public int $stopping_power;
    public string $type;

    /**
     * @var ?array<string, array<string, int|string>>
     */
    public static ?array $armor;

    public function __construct(public string $id)
    {
        $filename = config('app.data_path.cyberpunkred') . 'armor.php';
        self::$armor ??= require $filename;

        $id = strtolower($id);
        if (!isset(self::$armor[$id])) {
            throw new RuntimeException(sprintf(
                'Armor ID "%s" is invalid',
                $id
            ));
        }

        $armor = self::$armor[$id];
        $this->cost_category = CostCategory::from($armor['cost-category']);
        $this->description = $armor['description'];
        $this->page = $armor['page'];
        $this->penalty = $armor['penalty'];
        $this->ruleset = $armor['ruleset'];
        $this->stopping_power = $armor['stopping-power'];
        $this->type = $armor['type'];
    }

    public function __toString(): string
    {
        return $this->type;
    }

    public function getCost(): int
    {
        return $this->cost_category->marketPrice();
    }
}
