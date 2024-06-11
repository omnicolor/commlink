<?php

declare(strict_types=1);

namespace App\Models\Expanse;

use ArrayObject;
use TypeError;

/**
 * Collection of conditions.
 * @extends ArrayObject<int, Condition>
 * @psalm-suppress UnusedClass
 */
class ConditionArray extends ArrayObject
{
    /**
     * Add a condition to the array.
     * @param Condition $condition
     * @psalm-suppress ParamNameMismatch
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
