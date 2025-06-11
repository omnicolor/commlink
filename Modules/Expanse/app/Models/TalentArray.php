<?php

declare(strict_types=1);

namespace Modules\Expanse\Models;

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
    public function offsetSet(mixed $index, $talent): void
    {
        if ($talent instanceof Talent) {
            parent::offsetSet($index, $talent);
            return;
        }
        throw new TypeError('TalentArray only accepts Talent objects');
    }
}
