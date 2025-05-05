<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use ArrayObject;
use Override;
use TypeError;

/**
 * Collection of Skill objects.
 * @extends ArrayObject<int, Skill>
 */
final class SkillArray extends ArrayObject
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
        throw new TypeError('SkilllArray only accepts Skill objects');
    }
}
