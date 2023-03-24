<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5e;

/**
 * Base class for Shadowrun lifestyles.
 */
class Lifestyle
{
    /**
     * Base and maximum attributes for the lifestyle.
     * @var LifestyleAttributes
     */
    public LifestyleAttributes $attributes;

    /**
     * Cost of the lifestyle with no additional options.
     * @var int
     */
    public int $cost;

    /**
     * Description of the lifestyle.
     * @var string
     */
    public string $description;

    /**
     * Identifier for the lifestyle.
     * @var string
     */
    public string $id;

    /**
     * Name of the lifestyle.
     * @var string
     */
    public string $name;

    /**
     * Optional notes about the character's lifestyle.
     * @var string
     */
    public string $notes = '';

    /**
     * Options added to the lifestyle.
     * @var LifestyleOptionArray
     */
    public LifestyleOptionArray $options;

    /**
     * Number of months paid for this lifestyle.
     * @var int
     */
    public int $quantity;

    /**
     * Number of points that can be spent on upgrades.
     * @var int
     */
    public int $points;

    /**
     * Page the lifestyle was introduced on.
     * @var int
     */
    public int $page;

    /**
     * Ruleset the lifestyle was introduced in.
     * @var string
     */
    public string $ruleset;

    /**
     * Collection of all lifestyles.
     * @var ?array<string, mixed>
     */
    public static ?array $lifestyles;

    /**
     * Constructor.
     * @param string $id
     * @throws \RuntimeException
     */
    public function __construct(string $id)
    {
        $this->options = new LifestyleOptionArray();
        $filename = config('app.data_path.shadowrun5e') . 'lifestyles.php';
        self::$lifestyles ??= require $filename;

        $id = \strtolower($id);
        if (!\array_key_exists($id, self::$lifestyles)) {
            throw new \RuntimeException(
                \sprintf('Lifestyle ID "%s" is invalid', \urlencode($id))
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

    /**
     * Return the lifestyle's name.
     * @return string
     */
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * Return the monthly cost of the lifestyle.
     * @return int
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
     * @return int
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
     * @return LifestyleZone
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
            default => throw new \RuntimeException('Neighborhood rating out of range'),
        };
    }
}
