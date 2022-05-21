<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5e;

/**
 * Representation of something added to a lifestyle.
 */
class LifestyleOption
{
    /**
     * Nuyen cost of the option.
     * @var int
     */
    public ?int $cost;

    /**
     * Cost multiplier for the option.
     * @var float
     */
    public ?float $costMultiplier;

    /**
     * Description of the lifestyle option.
     * @var string
     */
    public string $description;

    /**
     * ID of the lifestyle option.
     * @var string
     */
    public string $id;

    /**
     * Minimum lifestyle required to have the cost covered.
     * @var string
     */
    public string $minimumLifestyle;

    /**
     * Name of the lifestyle option.
     * @var string
     */
    public string $name;

    /**
     * Page the lifestyle option was introduced on.
     * @var int
     */
    public int $page;

    /**
     * Number of points the option costs.
     * @var int
     */
    public int $points;

    /**
     * Ruleset the option was introduced in.
     * @var string
     */
    public string $ruleset;

    /**
     * Type of option: Asset, Outing, Service.
     * @var string
     */
    public string $type;

    /**
     * Collection of all options.
     * @var ?array<string, array<string, int|string|float>>
     */
    public static ?array $options;

    /**
     * Constructor.
     * @param string $id
     * @throws \RuntimeException
     */
    public function __construct(string $id)
    {
        $filename = config('app.data_path.shadowrun5e')
            . 'lifestyle-options.php';
        self::$options ??= require $filename;

        $id = \strtolower($id);
        if (!\array_key_exists($id, self::$options)) {
            throw new \RuntimeException(
                \sprintf('Lifestyle Option ID "%s" is invalid', $id)
            );
        }

        $option = self::$options[$id];
        $this->cost = $option['cost'] ?? null;
        $this->costMultiplier = $option['costMultiplier'] ?? null;
        $this->id = $id;
        $this->minimumLifestyle = $option['minimumLifestyle'];
        $this->name = $option['name'];
        $this->page = (int)$option['page'];
        $this->points = (int)$option['points'];
        $this->ruleset = $option['ruleset'];
        $this->type = $option['type'];
    }

    /**
     * Return the option as a string.
     * @return string
     */
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * Given a Lifestyle, determine whether the cost is covered by the
     * lifestyle or if it needs to be paid for.
     * @param Lifestyle $lifestyle
     * @return bool
     * @throws \RuntimeException
     */
    public function isCovered(Lifestyle $lifestyle): bool
    {
        switch ($this->minimumLifestyle) {
            case 'Commercial':
                return 'Commercial' === $lifestyle->name;
            case 'High':
                return 'High' === $lifestyle->name
                    || 'Luxury' === $lifestyle->name;
            case 'Low':
                return 'Low' === $lifestyle->name
                    || 'Middle' === $lifestyle->name
                    || 'High' === $lifestyle->name
                    || 'Luxury' === $lifestyle->name;
            case 'Luxury':
                return 'Luxury' === $lifestyle->name;
            case 'Middle':
                return 'Middle' === $lifestyle->name
                    || 'High' === $lifestyle->name
                    || 'Luxury' === $lifestyle->name;
            case 'None':
                return false;
            case 'Squatter':
                return 'Street' !== $lifestyle->name
                    && 'Commercial' !== $lifestyle->name
                    && 'Hospitalized' !== $lifestyle->name;
        }
        throw new \RuntimeException('Option has invalid minimum lifestyle');
    }

    /**
     * Return the actual cost of the option.
     *
     * If the option is covered by the attached lifestyle, the cost is zero.
     * Otherwise you need to pay for it.
     * @param Lifestyle $lifestyle
     * @return int
     */
    public function getCost(Lifestyle $lifestyle): int
    {
        if ($this->isCovered($lifestyle)) {
            return 0;
        }
        if (isset($this->costMultiplier)) {
            return (int)\floor($lifestyle->cost * $this->costMultiplier);
        }
        return (int)$this->cost;
    }
}
