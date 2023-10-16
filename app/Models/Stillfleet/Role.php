<?php

declare(strict_types=1);

namespace App\Models\Stillfleet;

use RuntimeException;

/**
 * The character's class (job, vocation, role).
 * @psalm-suppress PossiblyUnusedProperty
 * @psalm-suppress UnusedClass
 */
class Role
{
    public string $description;
    /** @var array<int, string> */
    public array $grit;
    public string $name;
    /** @var array<int, mixed> */
    public array $powerAdvanced;
    public string $powerMarquee;
    /** @var array<int, mixed> */
    public array $powerOptional;
    /** @var array<int, mixed> */
    public array $powerOther;
    /** @var array<int, string> */
    public array $responsibilities;

    /**
     * List of all roles.
     * @var ?array<string, array<string, mixed>>
     */
    public static ?array $roles;

    /**
     * @psalm-suppress PossiblyUnusedProperty
     */
    public function __construct(public string $id, public int $level)
    {
        $filename = config('app.data_path.stillfleet') . 'roles.php';
        self::$roles ??= require $filename;

        if (!isset(self::$roles[$id])) {
            throw new RuntimeException(\sprintf(
                'Role ID "%s" is invalid',
                $id
            ));
        }

        $role = self::$roles[$id];
        $this->description = $role['description'];
        $this->grit = $role['grit'];
        $this->name = $role['name'];
        /*
        $this->powerAdvanced;
        $this->powerMarquee;
        $this->powerOptional;
        $this->powerOther;
         */
        $this->responsibilities = $role['responsibilities'];
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
