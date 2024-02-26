<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5e;

use RuntimeException;

use function array_key_exists;
use function sprintf;
use function strtolower;
use function urlencode;

/**
 * Base class for Shadowrun lifestyles.
 * @psalm-suppress PossiblyUnusedProperty
 */
class Lifestyle
{
    /**
     * Base and maximum attributes for the lifestyle.
     */
    public LifestyleAttributes $attributes;

    /**
     * Cost of the lifestyle with no additional options.
     */
    public int $cost;

    /**
     * Description of the lifestyle.
     */
    public string $description;

    /**
     * Identifier for the lifestyle.
     */
    public string $id;

    /**
     * Name of the lifestyle.
     */
    public string $name;

    /**
     * Optional notes about the character's lifestyle.
     */
    public string $notes = '';

    /**
     * Options added to the lifestyle.
     */
    public LifestyleOptionArray $options;

    /**
     * Number of months paid for this lifestyle.
     */
    public int $quantity;

    /**
     * Number of points that can be spent on upgrades.
     */
    public int $points;

    /**
     * Page the lifestyle was introduced on.
     */
    public int $page;

    /**
     * Ruleset the lifestyle was introduced in.
     */
    public string $ruleset;

    /**
     * Collection of all lifestyles.
     * @var ?array<string, array<string, mixed>>
     */
    public static ?array $lifestyles;

    /**
     * Constructor.
     * @throws RuntimeException
     */
    public function __construct(string $id)
    {
        $this->options = new LifestyleOptionArray();
        $filename = config('app.data_path.shadowrun5e') . 'lifestyles.php';
        self::$lifestyles ??= require $filename;

        $id = strtolower($id);
        if (!array_key_exists($id, self::$lifestyles)) {
            throw new RuntimeException(
                sprintf('Lifestyle ID "%s" is invalid', urlencode($id))
            );
        }

        $lifestyle = self::$lifestyles[$id];
        $this->attributes = new LifestyleAttributes($lifestyle['attributes']);
        $this->cost = $lifestyle['cost'];
        $this->description = $lifestyle['description'];
        $this->id = $id;
        $this->name = $lifestyle['name'];
        $this->page = $lifestyle['page'];
        $this->points = $lifestyle['points'];
        $this->ruleset = $lifestyle['ruleset'];
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     * @return array<int, Lifestyle>
     */
    public static function all(): array
    {
        $filename = config('app.data_path.shadowrun5e') . 'lifestyles.php';
        self::$lifestyles ??= require $filename;

        $lifestyles = [];
        /** @var string $id */
        foreach (array_keys(self::$lifestyles) as $id) {
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
     * @psalm-suppress PossiblyUnusedMethod
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
