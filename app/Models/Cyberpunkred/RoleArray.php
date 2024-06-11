<?php

declare(strict_types=1);

namespace App\Models\Cyberpunkred;

use ArrayObject;
use TypeError;

/**
 * Collection of Roles.
 * @extends ArrayObject<int, Role>
 */
class RoleArray extends ArrayObject
{
    /**
     * Add a role to the array.
     * @param ?Role $role
     * @psalm-suppress ParamNameMismatch
     * @throws TypeError
     */
    public function offsetSet(mixed $index = null, mixed $role = null): void
    {
        if ($role instanceof Role) {
            parent::offsetSet($index, $role);
            return;
        }
        throw new TypeError('RoleArray only accepts Role objects');
    }
}
