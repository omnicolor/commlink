<?php

declare(strict_types=1);

namespace App\Models\Expanse;

use ArrayObject;
use TypeError;

/**
 * Collection of Focuses.
 * @extends ArrayObject<int, Focus>
 */
class FocusArray extends ArrayObject
{
    /**
     * Add a focus to the array.
     * @param ?int $index
     * @param Focus $focus
     * @throws TypeError
     */
    public function offsetSet($index, $focus): void
    {
        if (!($focus instanceof Focus)) {
            throw new TypeError('FocusArray only accepts Focus objects');
        }
        parent::offsetSet($index, $focus);
    }
}
