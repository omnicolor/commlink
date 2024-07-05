<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use RuntimeException;
use Stringable;

use function array_key_exists;
use function config;
use function floor;
use function sprintf;
use function strtolower;

/**
 * Representation of something added to a lifestyle.
 * @psalm-suppress PossiblyUnusedProperty
 */
class LifestyleOption implements Stringable
{
    /**
     * Nuyen cost of the option.
     */
    public ?int $cost;

    /**
     * Cost multiplier for the option.
     */
    public ?float $costMultiplier;

    /**
     * Description of the lifestyle option.
     */
    public string $description;

    /**
     * Minimum lifestyle required to have the cost covered.
     */
    public string $minimumLifestyle;

    /**
     * Name of the lifestyle option.
     */
    public string $name;

    /**
     * Page the lifestyle option was introduced on.
     */
    public int $page;

    /**
     * Number of points the option costs.
     */
    public int $points;

    /**
     * Ruleset the option was introduced in.
     */
    public string $ruleset;

    /**
     * Type of option: Asset, Outing, Service.
     */
    public string $type;

    /**
     * Collection of all options.
     * @var ?array<string, array<string, int|string|float>>
     */
    public static ?array $options = null;

    /**
     * @throws RuntimeException
     */
    public function __construct(public string $id)
    {
        $filename = config('shadowrun5e.data_path')
            . 'lifestyle-options.php';
        /** @psalm-suppress UnresolvableInclude */
        self::$options ??= require $filename;

        $id = strtolower($id);
        if (!array_key_exists($id, self::$options)) {
            throw new RuntimeException(
                sprintf('Lifestyle Option ID "%s" is invalid', $id)
            );
        }

        $option = self::$options[$id];
        $this->cost = $option['cost'] ?? null;
        $this->costMultiplier = $option['costMultiplier'] ?? null;
        $this->minimumLifestyle = $option['minimumLifestyle'];
        $this->name = $option['name'];
        $this->page = (int)$option['page'];
        $this->points = (int)$option['points'];
        $this->ruleset = $option['ruleset'];
        $this->type = $option['type'];
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * Given a Lifestyle, determine whether the cost is covered by the
     * lifestyle or if it needs to be paid for.
     * @throws RuntimeException
     */
    public function isCovered(Lifestyle $lifestyle): bool
    {
        return match ($this->minimumLifestyle) {
            'Commercial' => 'Commercial' === $lifestyle->name,
            'High' => 'High' === $lifestyle->name
                || 'Luxury' === $lifestyle->name,
            'Low' => 'Low' === $lifestyle->name
                || 'Middle' === $lifestyle->name
                || 'High' === $lifestyle->name
                || 'Luxury' === $lifestyle->name,
            'Luxury' => 'Luxury' === $lifestyle->name,
            'Middle' => 'Middle' === $lifestyle->name
                || 'High' === $lifestyle->name
                || 'Luxury' === $lifestyle->name,
            'None' => false,
            'Squatter' => 'Street' !== $lifestyle->name
                && 'Commercial' !== $lifestyle->name
                && 'Hospitalized' !== $lifestyle->name,
            default => throw new RuntimeException('Option has invalid minimum lifestyle'),
        };
    }

    /**
     * Return the actual cost of the option.
     *
     * If the option is covered by the attached lifestyle, the cost is zero.
     * Otherwise you need to pay for it.
     */
    public function getCost(Lifestyle $lifestyle): int
    {
        if ($this->isCovered($lifestyle)) {
            return 0;
        }
        if (isset($this->costMultiplier)) {
            return (int)floor($lifestyle->cost * $this->costMultiplier);
        }
        return (int)$this->cost;
    }
}
