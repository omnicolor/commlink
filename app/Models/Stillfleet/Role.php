<?php

declare(strict_types=1);

namespace App\Models\Stillfleet;

use Illuminate\Support\Facades\Log;
use RuntimeException;

use function sprintf;

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
    public int $page;
    /** @var array<int, string> */
    public array $power_advanced = [];
    public Power $power_marquee;
    /** @var array<string, Power> */
    public array $powers_optional = [];
    /** @var array<string, Power> */
    public array $powers_other = [];
    /** @var array<string, Power> */
    public array $powers_additional = [];
    /** @var array<int, string> */
    public array $responsibilities;
    public string $ruleset;

    /**
     * List of all roles.
     * @var ?array<string, array<string, mixed>>
     */
    public static ?array $roles;

    /**
     * @psalm-suppress PossiblyUnusedProperty
     * @param ?array<int, string> $powers
     */
    public function __construct(
        public string $id,
        public int $level,
        ?array $powers = [],
    ) {
        $filename = config('app.data_path.stillfleet') . 'roles.php';
        self::$roles ??= require $filename;

        if (!isset(self::$roles[$id])) {
            throw new RuntimeException(sprintf(
                'Role ID "%s" is invalid',
                $id
            ));
        }

        $role = self::$roles[$id];
        $this->description = $role['description'];
        $this->grit = $role['grit'];
        $this->name = $role['name'];
        $this->page = $role['page'];
        $this->power_advanced = $role['power-advanced'];
        try {
            $this->power_marquee = new Power($role['power-marquee']);
            // @codeCoverageIgnoreStart
        } catch (RuntimeException) {
            Log::warning(
                'Stillfleet role has invalid marquee power',
                [
                    'role' => $this->id,
                    'power' => $role['power-marquee'],
                ]
            );
        }
        // @codeCoverageIgnoreEnd
        /** @var string $powerId */
        foreach ($role['power-optional'] as $powerId) {
            try {
                $this->powers_optional[$powerId] = new Power($powerId);
                // @codeCoverageIgnoreStart
            } catch (RuntimeException) {
                Log::warning(
                    'Stillfleet role has invalid optional power',
                    [
                        'role' => $this->id,
                        'power' => $powerId,
                    ]
                );
            }
            // @codeCoverageIgnoreEnd
        }
        /** @var string $powerId */
        foreach ($role['power-other'] as $powerId) {
            try {
                $this->powers_other[$powerId] = new Power($powerId);
                // @codeCoverageIgnoreStart
            } catch (RuntimeException) {
                Log::warning(
                    'Stillfleet role has invalid other power',
                    [
                        'role' => $this->id,
                        'power' => $powerId,
                    ]
                );
            }
            // @codeCoverageIgnoreEnd
        }
        $this->responsibilities = $role['responsibilities'];
        $this->ruleset = $role['ruleset'];

        // Finally, add the character's additional powers, which may (or may
        // not) have anything to do with this role.
        foreach ($powers ?? [] as $powerId) {
            try {
                $this->powers_additional[$powerId] = new Power($powerId);
                // @codeCoverageIgnoreStart
            } catch (RuntimeException) {
                Log::warning(
                    'Stillfleet character has invalid additional power',
                    [
                        'role' => $this->id,
                        'power' => $powerId,
                    ]
                );
            }
            // @codeCoverageIgnoreEnd
        }
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @return array<string, Role>
     */
    public static function all(): array
    {
        $filename = config('app.data_path.stillfleet') . 'roles.php';
        self::$roles ??= require $filename;

        $roles = [];
        /** @var string $id */
        foreach (array_keys(self::$roles) as $id) {
            $roles[$id] = new Role($id, 1);
        }
        return $roles;
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     * @return array<string, Power>
     */
    public function powers(): array
    {
        return array_merge(
            [$this->power_marquee->id => $this->power_marquee],
            $this->powers_other,
            $this->powers_additional,
        );
    }
}
