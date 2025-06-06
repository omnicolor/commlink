<?php

declare(strict_types=1);

namespace Modules\Capers\Models;

use ArrayObject;
use Override;
use TypeError;

/**
 * Collection of Boosts.
 * @extends ArrayObject<int|string, Boost>
 */
class BoostArray extends ArrayObject
{
    /**
     * Add a boost to the array.
     * @param Boost $boost
     * @throws TypeError
     */
    #[Override]
    public function offsetSet(mixed $index = null, $boost = null): void
    {
        if ($boost instanceof Boost) {
            parent::offsetSet($index, $boost);
            return;
        }
        throw new TypeError('BoostArray only accepts Boost objects');
    }
}
