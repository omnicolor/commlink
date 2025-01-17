<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use ArrayObject;
use TypeError;

/**
 * Collection of Identities.
 * @extends ArrayObject<int, Identity>
 */
class IdentityArray extends ArrayObject
{
    /**
     * Add an identity to the array.
     * @param Identity $identity
     * @throws TypeError
     */
    public function offsetSet(mixed $index = null, $identity = null): void
    {
        if ($identity instanceof Identity) {
            parent::offsetSet($index, $identity);
            return;
        }
        throw new TypeError('IdentityArray only accepts Identity objects');
    }
}
