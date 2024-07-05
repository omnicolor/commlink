<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use ArrayObject;
use TypeError;

/**
 * Collection of Skill objects.
 * @extends ArrayObject<int, Skill>
 */
class SkillArray extends ArrayObject
{
    /**
     * Add a skill to the array.
     * @param Skill $skill
     * @psalm-suppress ParamNameMismatch
     * @throws TypeError
     */
    public function offsetSet(mixed $index = null, $skill = null): void
    {
        if ($skill instanceof Skill) {
            parent::offsetSet($index, $skill);
            return;
        }
        throw new TypeError('SkilllArray only accepts Skill objects');
    }
}
