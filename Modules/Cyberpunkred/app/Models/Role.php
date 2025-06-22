<?php

declare(strict_types=1);

namespace Modules\Cyberpunkred\Models;

use Error;
use Modules\Cyberpunkred\Models\Role\Exec;
use Modules\Cyberpunkred\Models\Role\Fixer;
use Modules\Cyberpunkred\Models\Role\Lawman;
use Modules\Cyberpunkred\Models\Role\Media;
use Modules\Cyberpunkred\Models\Role\Medtech;
use Modules\Cyberpunkred\Models\Role\Netrunner;
use Modules\Cyberpunkred\Models\Role\Nomad;
use Modules\Cyberpunkred\Models\Role\Rockerboy;
use Modules\Cyberpunkred\Models\Role\Solo;
use Modules\Cyberpunkred\Models\Role\Tech;
use RuntimeException;
use Stringable;

use function sprintf;
use function ucfirst;

abstract class Role implements Stringable
{
    protected const int DEFAULT_ROLE_RANK = 4;

    /**
     * Description of the role's ability.
     */
    public string $abilityDescription;

    /**
     * Name of the role's ability.
     */
    public string $abilityName;

    /**
     * Description of the role.
     */
    public string $description;

    /**
     * Rank the character has achieved in the role.
     */
    public int $rank;

    /**
     * Constructor.
     * @param array<string, mixed> $role
     */
    abstract public function __construct(array $role);

    abstract public function __toString(): string;

    /**
     * Return the appropriate role object from an array.
     * @param array<string, string|int> $role
     * @throws RuntimeException
     */
    public static function fromArray(array $role): Role
    {
        $class = sprintf(
            'Modules\\Cyberpunkred\\Models\\Role\\%s',
            ucfirst((string)$role['role'])
        );
        try {
            /** @var Role */
            $role = new $class($role);
            return $role;
        } catch (Error) {
            throw new RuntimeException(sprintf(
                'Role "%s" is invalid',
                $role['role']
            ));
        }
    }

    /**
     * Return a collection of all roles.
     */
    public static function all(): RoleArray
    {
        $roles = new RoleArray();
        $roles[] = new Exec([]);
        $roles[] = new Fixer([]);
        $roles[] = new Lawman([]);
        $roles[] = new Media([]);
        $roles[] = new Medtech([]);
        $roles[] = new Netrunner([]);
        $roles[] = new Nomad([]);
        $roles[] = new Rockerboy([]);
        $roles[] = new Solo([]);
        $roles[] = new Tech([]);
        return $roles;
    }
}
