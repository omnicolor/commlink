<?php

declare(strict_types=1);

namespace App\Models\Capers;

use ArrayObject;
use TypeError;

/**
 * Collection of Boosts.
 * @extends ArrayObject<int|string, Boost>
 */
class BoostArray extends ArrayObject
{
    /**
     * Add a boost to the array.
     * @param int|null|string $index
     * @param Boost $boost
     * @throws TypeError
     */
    public function offsetSet($index = null, $boost = null): void
    {
        if ($boost instanceof Boost) {
            parent::offsetSet($index, $boost);
            return;
        }
        throw new TypeError('BoostArray only accepts Boost objects');
    }
}
