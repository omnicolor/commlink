<?php

declare(strict_types=1);

namespace Modules\Capers\Models;

use ArrayObject;
use Override;
use TypeError;

/**
 * Collection of Powers.
 * @extends ArrayObject<int|string, Power>
 */
class PowerArray extends ArrayObject
{
    /**
     * Add a power to the array.
     * @param Power $power
     * @throws TypeError
     */
    #[Override]
    public function offsetSet(mixed $index = null, $power = null): void
    {
        if ($power instanceof Power) {
            parent::offsetSet($index, $power);
            return;
        }
        throw new TypeError('PowerArray only accepts Power objects');
    }
}
