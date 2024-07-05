<?php

declare(strict_types=1);

namespace Modules\Expanse\Models;

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
     * @param Talent $talent
     * @psalm-suppress ParamNameMismatch
     * @throws TypeError
     */
    public function offsetSet(mixed $index, $talent): void
    {
        if ($talent instanceof Talent) {
            parent::offsetSet($index, $talent);
            return;
        }
        throw new TypeError('TalentArray only accepts Talent objects');
    }
}
