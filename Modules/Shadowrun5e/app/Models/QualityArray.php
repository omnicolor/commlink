<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use ArrayObject;
use TypeError;

/**
 * Collection of qualities.
 * @extends ArrayObject<int, Quality>
 */
class QualityArray extends ArrayObject
{
    /**
     * Add a quality to the array.
     * @param Quality $quality
     * @throws TypeError
     */
    public function offsetSet(mixed $index = null, $quality = null): void
    {
        if ($quality instanceof Quality) {
            parent::offsetSet($index, $quality);
            return;
        }
        throw new TypeError('QualityArray only accepts Quality objects');
    }
}
