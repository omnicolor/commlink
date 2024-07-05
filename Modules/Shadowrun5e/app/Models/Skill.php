<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use Stringable;

/**
 * Base class for active and knowledge skills.
 */
abstract class Skill implements Stringable
{
    /**
     * Attribute linked to this skill.
     * @psalm-suppress PossiblyUnusedProperty
     */
    public string $attribute;

    /**
     * Level of the skill, N for native language.
     */
    public int | string $level;

    /**
     * Name of the skill.
     */
    public string $name;

    /**
     * Specialization(s) if any, comma-separated.
     */
    public ?string $specialization;

    /**
     * Limit the skill uses.
     */
    public string $limit;

    public function __toString(): string
    {
        return $this->name;
    }
}
