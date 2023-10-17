<?php

declare(strict_types=1);

namespace App\Models\Expanse;

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
     * @param ?int $index
     * @param Talent $talent
     * @throws TypeError
     */
    public function offsetSet($index, $talent): void
    {
        if (!($talent instanceof Talent)) {
            throw new TypeError('TalentArray only accepts Talent objects');
        }
        parent::offsetSet($index, $talent);
    }
}
