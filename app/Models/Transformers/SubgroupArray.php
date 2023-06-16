<?php

declare(strict_types=1);

namespace App\Models\Transformers;

use ArrayObject;
use TypeError;

/**
 * Collection of Subgroups.
 * @extends ArrayObject<int, Subgroup>
 */
class SubgroupArray extends ArrayObject
{
    /**
     * @param int|null|string $index
     * @param Subgroup $subgroup
     * @throws TypeError
     */
    public function offsetSet($index = null, $subgroup = null): void
    {
        if ($subgroup instanceof Subgroup) {
            parent::offsetSet($index, $subgroup);
            return;
        }
        throw new TypeError('SubgroupArray only accepts Subgroup objects');
    }
}
