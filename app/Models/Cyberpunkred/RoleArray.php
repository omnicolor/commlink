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
     * @param ?int $index
     * @param ?Role $role
     * @throws TypeError
     */
    public function offsetSet($index = null, mixed $role = null): void
    {
        if (!($role instanceof Role)) {
            throw new TypeError('RoleArray only accepts Role objects');
        }
        parent::offsetSet($index, $role);
    }
}
