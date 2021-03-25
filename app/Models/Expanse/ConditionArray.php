<?php

declare(strict_types=1);

namespace App\Models\Expanse;

/**
 * Collection of conditions.
 * @extends \ArrayObject<int, Condition>
 */
class ConditionArray extends \ArrayObject
{
    /**
     * Add a condition to the array.
     * @param ?int $index
     * @param Condition $condition
     * @throws \TypeError
     */
    public function offsetSet($index, $condition): void
    {
        if (!($condition instanceof Condition)) {
            throw new \TypeError('ConditionArray only accepts Condition objects');
        }
        parent::offsetSet($index, $condition);
    }
}
