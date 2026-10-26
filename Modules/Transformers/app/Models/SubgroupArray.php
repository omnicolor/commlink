<?php

declare(strict_types=1);

namespace Modules\Transformers\Models;

use ArrayObject;
use Override;
use TypeError;

/**
 * Collection of Subgroups.
 * @extends ArrayObject<int|string, Subgroup>
 */
class SubgroupArray extends ArrayObject
{
    /**
     * @param int|string|null $key
     * @param Subgroup $value
     * @throws TypeError
     */
    #[Override]
    public function offsetSet($key = null, $value = null): void
    {
        if ($value instanceof Subgroup) {
            parent::offsetSet($key, $value);
            return;
        }
        throw new TypeError('SubgroupArray only accepts Subgroup objects');
    }
}
