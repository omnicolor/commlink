<?php

declare(strict_types=1);

namespace App\Models\StarTrekAdventures;

use ArrayObject;
use TypeError;

/**
 * Collection of Talents.
 * @extends ArrayObject<int, Talent>
 */
class TalentArray extends ArrayObject
{
    /**
     * Add a talent to the array.
     * @param mixed $index
     * @param Talent $talent
     * @throws TypeError
     */
    public function offsetSet($index = null, $talent = null): void
    {
        if (!($talent instanceof Talent)) {
            throw new TypeError('TalentArray only accepts Talent objects');
        }
        parent::offsetSet($index, $talent);
    }
}
