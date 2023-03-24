<?php

declare(strict_types=1);

namespace App\Models\Cyberpunkred;

use Error;
use RuntimeException;

abstract class Role
{
    protected const DEFAULT_ROLE_RANK = 4;

    /**
     * Description of the role's ability.
     * @var string
     */
    public string $abilityDescription;

    /**
     * Name of the role's ability.
     * @var string
     */
    public string $abilityName;

    /**
     * Description of the role.
     * @var string
     */
    public string $description;

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
            'App\\Models\\Cyberpunkred\\Role\\%s',
            \ucfirst((string)$role['role'])
        );
        try {
            /** @var Role */
            $role = new $class($role);
            return $role;
        } catch (Error) {
            throw new RuntimeException(\sprintf(
                'Role "%s" is invalid',
                $role['role']
            ));
        }
    }

    /**
     * Return a collection of all roles.
     * @return RoleArray
     */
    public static function all(): RoleArray
    {
        $roles = new RoleArray();
        $roles[] = new Role\Exec([]);
        $roles[] = new Role\Fixer([]);
        $roles[] = new Role\Lawman([]);
        $roles[] = new Role\Media([]);
        $roles[] = new Role\Medtech([]);
        $roles[] = new Role\Netrunner([]);
        $roles[] = new Role\Nomad([]);
        $roles[] = new Role\Rockerboy([]);
        $roles[] = new Role\Solo([]);
        $roles[] = new Role\Tech([]);
        return $roles;
    }
}
