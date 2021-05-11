<?php

declare(strict_types=1);

namespace App\Models\CyberpunkRed;

abstract class Role
{
    /**
     * Rank the character has achieved in the role.
     * @var int
     */
    public int $rank;

    /**
     * Constructor.
     * @param array<string, mixed> $role
     */
    abstract public function __construct(array $role);

    /**
     * Return the role's name as a string.
     * @return string
     */
    abstract public function __toString(): string;

    /**
     * Return the appropriate role object from an array.
     * @param array<string, string|int> $role
     * @return Role
     */
    public static function fromArray(array $role): Role
    {
        $class = \sprintf(
            'App\\Models\\CyberpunkRed\\Role\\%s',
            \ucfirst((string)$role['role'])
        );
        try {
            return new $class($role);
        } catch (\Error $ex) {
            throw new \RuntimeException(\sprintf(
                'Role "%s" is invalid',
                $role['role']
            ));
        }
    }
}
