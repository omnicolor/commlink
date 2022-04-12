<?php

declare(strict_types=1);

namespace App\Models\Capers;

/**
 * Collection of Skills.
 * @extends \ArrayObject<int|string, Skill>
 */
class SkillArray extends \ArrayObject
{
    /**
     * Add a skill to the array.
     * @param int|null|string $index
     * @param Skill $skill
     * @throws \TypeError
     */
    public function offsetSet($index = null, $skill = null): void
    {
        if ($skill instanceof Skill) {
            parent::offsetSet($index, $skill);
            return;
        }
        throw new \TypeError('SkillArray only accepts Skill objects');
    }
}
