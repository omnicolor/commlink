<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5E;

/**
 * Collection of Skill objects.
 * @extends \ArrayObject<int, Skill>
 */
class SkillArray extends \ArrayObject
{
    /**
     * Add a skill to the array.
     * @param ?int $index
     * @param Skill $skill
     * @throws \TypeError
     */
    public function offsetSet($index = null, $skill = null): void
    {
        if ($skill instanceof Skill) {
            parent::offsetSet($index, $skill);
            return;
        }
        throw new \TypeError('SkilllArray only accepts Skill objects');
    }
}
