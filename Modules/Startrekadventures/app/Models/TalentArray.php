<?php

declare(strict_types=1);

namespace Modules\Startrekadventures\Models;

use ArrayObject;
use Override;
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
     * @throws TypeError
     */
    #[Override]
    public function offsetSet(mixed $index = null, $talent = null): void
    {
        if (!($talent instanceof Talent)) {
            throw new TypeError('TalentArray only accepts Talent objects');
        }
        parent::offsetSet($index, $talent);
    }
}
