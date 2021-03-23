<?php

declare(strict_types=1);

namespace App\Models\CyberpunkRed;

/**
 * Collection of Skills.
 * @extends \ArrayObject<int, Skill>
 */
class SkillArray extends \ArrayObject
{
    /**
     * Add a skill to the array.
     * @param ?int $index
     * @param ?Skill $skill
     * @throws \TypeError
     */
    public function offsetSet($index = null, $skill = null): void
    {
        if (!($skill instanceof Skill)) {
            throw new \TypeError('SkillArray only accepts Skill objects');
        }
        parent::offsetSet($index, $skill);
    }
}
