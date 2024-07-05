<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use RuntimeException;
use Stringable;

use function config;
use function sprintf;
use function strtolower;

/**
 * Something to change a piece of armor's behavior.
 */
class ArmorModification implements Stringable
{
    /**
     * Availability code for the modification.
     * @psalm-suppress PossiblyUnusedProperty
     */
    public ?string $availability;

    /**
     * Cost of the modification.
     */
    public int $cost;

    /**
     * Cost modifier for the modification.
     */
    public float $costModifier;

    /**
     * Description of the modification.
     * @psalm-suppress PossiblyUnusedProperty
     */
    public string $description;

    /**
     * List of effects for the modification.
     * @var array<string, int>
     */
    public array $effects = [];

    /**
     * List of modifications this is incompatible with.
     * @psalm-suppress PossiblyUnusedProperty
     * @var array<int, string>
     */
    public array $incompatibleWith = [];

    /**
     * Name of the modification.
     */
    public string $name;

    /**
     * Rating for the modification.
     * @psalm-suppress PossiblyUnusedProperty
     */
    public ?int $rating;

    /**
     * Ruleset the modification comes from.
     * @psalm-suppress PossiblyUnusedProperty
     */
    public string $ruleset = 'core';

    /**
     * List of all modifications.
     * @var ?array<mixed>
     */
    public static ?array $modifications;

    /**
     * Construct a new modification object.
     * @throws RuntimeException
     */
    public function __construct(public string $id)
    {
        $filename = config('shadowrun5e.data_path')
            . 'armor-modifications.php';
        /** @psalm-suppress UnresolvableInclude */
        self::$modifications ??= require $filename;

        $id = strtolower($id);
        if (!isset(self::$modifications[$id])) {
            throw new RuntimeException(sprintf(
                'Modification ID "%s" not found',
                $id
            ));
        }

        $mod = self::$modifications[$id];
        $this->availability = $mod['availability'];
        if (isset($mod['cost'])) {
            $this->cost = $mod['cost'];
        } else {
            $this->cost = 0;
            $this->costModifier = $mod['cost-multiplier'];
        }
        $this->description = $mod['description'];
        $this->effects = $mod['effects'] ?? [];
        $this->id = $mod['id'];
        $this->name = $mod['name'];
        $this->rating = $mod['rating'] ?? null;
        $this->ruleset = $mod['ruleset'] ?? 'core';
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getCost(Armor $armor): int
    {
        if (0 !== $this->cost) {
            return $this->cost;
        }
        return (int)(($armor->cost * $this->costModifier) - $armor->cost);
    }

    /**
     * Find a modification by its name, and optional rating.
     * @throws RuntimeException
     */
    public static function findByName(
        string $name,
        ?int $rating = null,
    ): ArmorModification {
        $filename = config('shadowrun5e.data_path')
            . 'armor-modifications.php';
        /** @psalm-suppress UnresolvableInclude */
        self::$modifications ??= require $filename;

        foreach (self::$modifications as $mod) {
            if (strtolower((string)$mod['name']) !== strtolower($name)) {
                continue;
            }
            if (null !== $rating && $rating !== $mod['rating']) {
                continue; // @codeCoverageIgnore
            }
            return new self($mod['id']);
        }

        throw new RuntimeException(sprintf(
            'Armor modification "%s" was not found',
            $name
        ));
    }
}
