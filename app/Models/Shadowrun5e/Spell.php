<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5e;

use RuntimeException;

/**
 * Class representing a spell in Shadowrun 5E.
 * @psalm-suppress PossiblyUnusedProperty
 */
class Spell
{
    use ForceTrait;

    /**
     * Category (combat, detection, etc).
     */
    public string $category;

    /**
     * Damage type for the spell.
     */
    public ?string $damage;

    /**
     * Description of the spell.
     */
    public string $description;

    /**
     * Drain code for the spell.
     */
    public string $drain;

    /**
     * Duration of the spell.
     */
    public string $duration;

    /**
     * Force of the spell.
     */
    public int $force;

    /**
     * Unique ID of the spell.
     */
    public string $id;

    /**
     * Name of the spell.
     */
    public string $name;

    /**
     * Page the spell was introduced on.
     */
    public ?int $page;

    /**
     * Range of the spell (T, LOS, etc).
     */
    public string $range;

    /**
     * Book ID the spell was introduced in.
     */
    public string $ruleset;

    /**
     * List of tags for the spell.
     * @var array<int, string>
     */
    public array $tags = [];

    /**
     * Type of the spell.
     */
    public string $type;

    /**
     * List of all spells.
     * @var ?array<string, array<string, mixed>>
     */
    public static ?array $spells;

    /**
     * Construct a new spell object.
     * @throws RuntimeException if the ID is invalid
     */
    public function __construct(string $id)
    {
        $filename = config('app.data_path.shadowrun5e') . 'spells.php';
        self::$spells ??= require $filename;

        $id = \strtolower($id);
        if (!isset(self::$spells[$id])) {
            throw new RuntimeException(
                \sprintf('Spell ID "%s" is invalid', $id)
            );
        }

        $spell = self::$spells[$id];
        $this->category = $spell['category'];
        $this->damage = $spell['damage'] ?? '';
        $this->description = $spell['description'];
        $this->drain = $spell['drain'];
        $this->duration = $spell['duration'];
        $this->id = $id;
        $this->name = $spell['name'];
        $this->page = $spell['page'] ?? null;
        $this->range = $spell['range'];
        $this->ruleset = $spell['ruleset'];
        $this->tags = $spell['tags'];
        $this->type = $spell['type'];
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * Try to find a spell by its name.
     * @throws RuntimeException
     */
    public static function findByName(string $name): Spell
    {
        $filename = config('app.data_path.shadowrun5e') . 'spells.php';
        self::$spells ??= require $filename;

        foreach (self::$spells as $spell) {
            if (\strtolower($name) === \strtolower($spell['name'])) {
                return new Spell($spell['id']);
            }
        }

        throw new RuntimeException(\sprintf(
            'Spell "%s" was not found',
            $name
        ));
    }

    /**
     * Return the drain value for the spell, based on its force.
     * @psalm-suppress PossiblyUnusedMethod
     * @throws RuntimeException if the force isn't set
     */
    public function getDrain(): int
    {
        if (!isset($this->force)) {
            throw new RuntimeException('Force has not been set');
        }
        return $this->convertFormula($this->drain, 'F', $this->force);
    }

    /**
     * Set the force of the spell.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function setForce(int $force): Spell
    {
        $this->force = $force;
        return $this;
    }
}
