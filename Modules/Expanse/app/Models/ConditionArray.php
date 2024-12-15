<?php

declare(strict_types=1);

namespace Modules\Expanse\Models;

use ArrayObject;
use TypeError;

/**
 * Collection of conditions.
 * @extends ArrayObject<int, Condition>
 */
class ConditionArray extends ArrayObject
{
    /**
     * Add a condition to the array.
     * @param Condition $condition
     * @throws TypeError
     */
    public function offsetSet(mixed $index, $condition): void
    {
        if ($condition instanceof Condition) {
            parent::offsetSet($index, $condition);
            return;
        }
        throw new TypeError('ConditionArray only accepts Condition objects');
    }
}
