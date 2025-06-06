<?php

declare(strict_types=1);

namespace Modules\Capers\Models;

use ArrayObject;
use Override;
use TypeError;

/**
 * Collection of Skills.
 * @extends ArrayObject<int|string, Skill>
 */
class SkillArray extends ArrayObject
{
    /**
     * Add a skill to the array.
     * @param Skill $skill
     * @throws TypeError
     */
    #[Override]
    public function offsetSet(mixed $index = null, $skill = null): void
    {
        if ($skill instanceof Skill) {
            parent::offsetSet($index, $skill);
            return;
        }
        throw new TypeError('SkillArray only accepts Skill objects');
    }
}
