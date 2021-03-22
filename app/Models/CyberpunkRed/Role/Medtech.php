<?php

declare(strict_types=1);

namespace App\Models\CyberpunkRed\Role;

use App\Models\CyberpunkRed\Role;

class Medtech extends Role
{
    /**
     * Constructor.
     * @param array<string, int> $role
     */
    public function __construct(array $role)
    {
        $this->rank = $role['rank'];
    }

    /**
     * Return the name of the role.
     * @return string
     */
    public function __toString(): string
    {
        return 'Medtech';
    }
}
