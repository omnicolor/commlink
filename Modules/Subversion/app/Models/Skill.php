<?php

declare(strict_types=1);

namespace Modules\Subversion\Models;

use RuntimeException;

use function sprintf;

/**
 * @psalm-suppress PossiblyUnusedProperty
 */
class Skill
{
    public string $description;
    public string $name;
    public int $page;
    public string $ruleset;

    /**
     * @var array<int, string>
     */
    public array $attributes;

    /**
     * @var ?array<string, array<string, array<int, string>|int|string>>
     */
    public static ?array $skills;

    /**
     * @psalm-suppress PossiblyUnusedProperty
     */
    public function __construct(public string $id, public ?int $rank = null)
    {
        $filename = config('subversion.data_path') . 'skills.php';
        /** @psalm-suppress UnresolvableInclude */
        self::$skills ??= require $filename;

        if (!isset(self::$skills[$id])) {
            throw new RuntimeException(sprintf('Skill "%s" not found', $id));
        }

        $skill = self::$skills[$id];
        $this->attributes = $skill['attributes'];
        $this->description = $skill['description'];
        $this->name = $skill['name'];
        $this->page = $skill['page'];
        $this->ruleset = $skill['ruleset'];
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     * @return array<string, Skill>
     */
    public static function all(): array
    {
        $filename = config('subversion.data_path') . 'skills.php';
        /** @psalm-suppress UnresolvableInclude */
        self::$skills ??= require $filename;

        $skills = [];
        foreach (self::$skills as $skill) {
            $skills[$skill['id']] = new Skill($skill['id']);
        }
        // @phpstan-ignore-next-line
        return $skills;
    }
}
