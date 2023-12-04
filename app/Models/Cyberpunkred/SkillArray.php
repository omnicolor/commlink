<?php

declare(strict_types=1);

namespace App\Models\Cyberpunkred;

use ArrayObject;
use TypeError;

/**
 * Collection of Skills.
 * @extends ArrayObject<int|string, Skill>
 */
class SkillArray extends ArrayObject
{
    /**
     * Add a skill to the array.
     * @param int|null|string $index
     * @param ?Skill $skill
     * @psalm-suppress ParamNameMismatch
     * @throws TypeError
     */
    public function offsetSet($index = null, $skill = null): void
    {
        if (!($skill instanceof Skill)) {
            throw new TypeError('SkillArray only accepts Skill objects');
        }
        parent::offsetSet($index, $skill);
    }
}
