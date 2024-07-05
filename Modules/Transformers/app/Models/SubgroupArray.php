<?php

declare(strict_types=1);

namespace Modules\Transformers\Models;

use ArrayObject;
use TypeError;

/**
 * Collection of Subgroups.
 * @extends ArrayObject<int, Subgroup>
 */
class SubgroupArray extends ArrayObject
{
    /**
     * @param int|null|string $offset
     * @param Subgroup $value
     * @throws TypeError
     */
    public function offsetSet($offset = null, $value = null): void
    {
        if ($value instanceof Subgroup) {
            parent::offsetSet($offset, $value);
            return;
        }
        throw new TypeError('SubgroupArray only accepts Subgroup objects');
    }
}
