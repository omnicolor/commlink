<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5E;

/**
 * Class representing a spell in Shadowrun 5E.
 */
class Spell
{
    use ForceTrait;

    /**
     * Category (combat, detection, etc).
     * @var string
     */
    public string $category;

    /**
     * Damage type for the spell.
     * @var ?string
     */
    public ?string $damage;

    /**
     * Description of the spell.
     * @var string
     */
    public string $description;

    /**
     * Drain code for the spell.
     * @var string
     */
    public string $drain;

    /**
     * Duration of the spell.
     * @var string
     */
    public string $duration;

    /**
     * Force of the spell.
     * @var int
     */
    public int $force;

    /**
     * Unique ID of the spell.
     * @var string
     */
    public string $id;

    /**
     * Name of the spell.
     * @var string
     */
    public string $name;

    /**
     * Page the spell was introduced on.
     * @var ?int
     */
    public ?int $page;

    /**
     * Range of the spell (T, LOS, etc).
     * @var string
     */
    public string $range;

    /**
     * Book ID the spell was introduced in.
     * @var string
     */
    public string $ruleset;

    /**
     * List of tags for the spell.
     * @var string[]
     */
    public array $tags = [];

    /**
     * Type of the spell.
     * @var string
     */
    public string $type;

    /**
     * List of all spells.
     * @var ?array<mixed>
     */
    public static ?array $spells;

    /**
     * Construct a new spell object.
     * @param string $id ID to load
     * @throws \RuntimeException if the ID is invalid
     */
    public function __construct(string $id)
    {
        $filename = config('app.data_path.shadowrun5e') . 'spells.php';
        self::$spells ??= require $filename;

        $id = strtolower($id);
        if (!isset(self::$spells[$id])) {
            throw new \RuntimeException(
                sprintf('Spell ID "%s" is invalid', $id)
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

    /**
     * Return the name of the spell.
     * @return string
     */
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * Return the drain value for the spell, based on its force.
     * @return int
     * @throws \RuntimeException if the force isn't set
     */
    public function getDrain(): int
    {
        if (!isset($this->force)) {
            throw new \RuntimeException('Force has not been set');
        }
        return $this->convertFormula($this->drain, 'F', $this->force);
    }

    /**
     * Set the force of the spell.
     * @param int $force
     * @return Spell
     */
    public function setForce(int $force): Spell
    {
        $this->force = $force;
        return $this;
    }
}
