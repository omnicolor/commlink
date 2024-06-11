<?php

declare(strict_types=1);

namespace App\Models\Cyberpunkred;

use Illuminate\Support\Str;
use RuntimeException;
use Stringable;

use function config;
use function sprintf;
use function strtolower;
use function ucfirst;

/**
 * @psalm-suppress PossiblyUnusedProperty
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
        if ($armor['cost-category'] instanceof CostCategory) {
            $this->cost_category = $armor['cost-category'];
        } else {
            $this->cost_category = CostCategory::from(
                ucfirst($armor['cost-category'])
            );
        }
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

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public static function findByName(string $name): self
    {
        $filename = config('app.data_path.cyberpunkred') . 'armor.php';
        self::$armor ??= require $filename;

        $lowerName = Str::lower($name);
        foreach (self::$armor as $id => $armor) {
            if (Str::lower($armor['type']) !== $lowerName) {
                continue;
            }
            return new self($id);
        }
        throw new RuntimeException(sprintf('Armor "%s" was not found', $name));
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function getCost(): int
    {
        return $this->cost_category->marketPrice();
    }
}
