<?php

declare(strict_types=1);

namespace App\Models\Subversion;

use RuntimeException;
use Stringable;

use function array_keys;
use function sprintf;

/**
 * @psalm-suppress PossiblyUnusedProperty
 */
class RelationLevel implements Stringable
{
    public const string MINOR = 'minor';
    public const string NORMAL = 'normal';
    public const string MAJOR = 'major';

    public int $cost;
    public string $description;
    public string $level;
    public string $name;
    public int $page;
    public int $power;
    public int $regard;
    public string $ruleset;

    /**
     * @var array<string, array<string, int|string>>
     */
    public static ?array $levels;

    /**
     * @psalm-suppress PossiblyUnusedProperty
     */
    public function __construct(public string $id)
    {
        $filename = config('app.data_path.subversion') . 'relation-levels.php';
        /** @psalm-suppress UnresolvableInclude */
        self::$levels ??= require $filename;

        if (!isset(self::$levels[$id])) {
            throw new RuntimeException(
                sprintf('Relation level "%s" not found', $id)
            );
        }

        $level = self::$levels[$id];
        $this->cost = $level['cost'];
        $this->description = $level['description'];
        $this->level = $level['level'];
        $this->name = $level['name'];
        $this->page = $level['page'];
        $this->power = $level['power'];
        $this->regard = $level['regard'];
        $this->ruleset = $level['ruleset'];
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @return array<int, RelationLevel>
     */
    public static function all(): array
    {
        $filename = config('app.data_path.subversion') . 'relation-levels.php';
        /** @psalm-suppress UnresolvableInclude */
        self::$levels ??= require $filename;

        $levels = [];
        /** @var string $level */
        foreach (array_keys(self::$levels) as $level) {
            $levels[] = new self($level);
        }
        return $levels;
    }

    /**
     * @psalm-suppress PossiblyUnusedReturnValue
     */
    public static function sort(self $a, self $b): int
    {
        if ($a->level === $b->level) {
            return $a->name <=> $b->name;
        }
        if (RelationLevel::MINOR === $a->level || RelationLevel::MAJOR === $b->level) {
            return -1;
        }
        return 1;
    }
}
