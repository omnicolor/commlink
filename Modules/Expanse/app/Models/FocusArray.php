<?php

declare(strict_types=1);

namespace Modules\Expanse\Models;

use ArrayObject;
use Override;
use TypeError;

/**
 * Collection of Focuses.
 * @extends ArrayObject<int, Focus>
 */
class FocusArray extends ArrayObject
{
    /**
     * Add a focus to the array.
     * @param Focus $focus
     * @throws TypeError
     */
    #[Override]
    public function offsetSet(mixed $index, $focus): void
    {
        if ($focus instanceof Focus) {
            parent::offsetSet($index, $focus);
            return;
        }
        throw new TypeError('FocusArray only accepts Focus objects');
    }
}
