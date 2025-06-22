<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use Override;
use RuntimeException;
use Stringable;

use function config;
use function sprintf;
use function strtolower;

/**
 * Base class for Shadowrun lifestyles.
 */
final class Lifestyle implements Stringable
{
    /**
     * Base and maximum attributes for the lifestyle.
     */
    public LifestyleAttributes $attributes;
    public readonly int $cost;
    public readonly string $description;
    public readonly string $name;
    public string $notes = '';

    /**
     * Options added to the lifestyle.
     */
    public LifestyleOptionArray $options;

    /**
     * Number of months paid for this lifestyle.
     */
    public int $quantity;
    public readonly int $points;
    public readonly int $page;
    public readonly string $ruleset;

    /**
     * Collection of all lifestyles.
     * @var ?array<string, array<string, mixed>>
     */
    public static ?array $lifestyles = null;

    /**
     * @throws RuntimeException
     */
    public function __construct(public readonly string $id)
    {
        $this->options = new LifestyleOptionArray();
        $filename = config('shadowrun5e.data_path') . 'lifestyles.php';
        self::$lifestyles ??= require $filename;

        $id = strtolower($id);
        if (!isset(self::$lifestyles[$id])) {
            throw new RuntimeException(
                sprintf('Lifestyle ID "%s" is invalid', $id)
            );
        }

        $lifestyle = self::$lifestyles[$id];
        $this->attributes = new LifestyleAttributes($lifestyle['attributes']);
        $this->cost = $lifestyle['cost'];
        $this->description = $lifestyle['description'];
        $this->name = $lifestyle['name'];
        $this->page = $lifestyle['page'];
        $this->points = $lifestyle['points'];
        $this->ruleset = $lifestyle['ruleset'];
    }

    #[Override]
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @return array<int, Lifestyle>
     */
    public static function all(): array
    {
        $filename = config('shadowrun5e.data_path') . 'lifestyles.php';
        self::$lifestyles ??= require $filename;

        $lifestyles = [];
        /** @var string $id */
        foreach (array_keys(self::$lifestyles ?? []) as $id) {
            $lifestyles[] = new self($id);
        }
        return $lifestyles;
    }

    /**
     * Return the monthly cost of the lifestyle.
     */
    public function getCost(): int
    {
        $cost = $this->cost;
        foreach ($this->options as $option) {
            $cost += $option->getCost($this);
        }
        return $cost;
    }

    /**
     * Return the neighborhood rating for the lifestyle.
     */
    public function getNeighborhood(): int
    {
        $value = $this->attributes->neighborhood;
        foreach ($this->options as $option) {
            if ('Increase Neighborhood' !== $option->name) {
                continue;
            }
            $value++;
        }
        return $value;
    }

    /**
     * Return the lifestyle's zone.
     */
    public function getZone(): LifestyleZone
    {
        return match ($this->getNeighborhood()) {
            0 => new LifestyleZone('z'),
            1 => new LifestyleZone('e'),
            2 => new LifestyleZone('d'),
            3 => new LifestyleZone('c'),
            4 => new LifestyleZone('b'),
            5 => new LifestyleZone('a'),
            6 => new LifestyleZone('aa'),
            7 => new LifestyleZone('aaa'),
            default => throw new RuntimeException('Neighborhood rating out of range'),
        };
    }
}
