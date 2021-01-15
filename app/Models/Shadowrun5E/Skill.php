<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5E;

/**
 * Base class for active and knowledge skills.
 */
abstract class Skill
{
    /**
     * Attribute linked to this skill.
     * @var string
     */
    public string $attribute;

    /**
     * Level of the skill, N for native language
     * @var int|string
     */
    public $level;

    /**
     * Name of the skill
     * @var string
     */
    public string $name;

    /**
     * Specialization(s) if any, comma-separated
     * @var ?string
     */
    public ?string $specialization;

    /**
     * Limit the skill uses
     * @var string
     */
    public string $limit;

    /**
     * Return the name of the skill.
     * @return string
     */
    public function __toString(): string
    {
        return $this->name;
    }
}
