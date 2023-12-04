<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5e;

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
     * @param ?int $index
     * @param Quality $quality
     * @psalm-suppress ParamNameMismatch
     * @throws TypeError
     */
    public function offsetSet($index = null, $quality = null): void
    {
        if ($quality instanceof Quality) {
            parent::offsetSet($index, $quality);
            return;
        }
        throw new TypeError('QualityArray only accepts Quality objects');
    }
}
