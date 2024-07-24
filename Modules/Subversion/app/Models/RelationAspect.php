<?php

declare(strict_types=1);

namespace Modules\Subversion\Models;

use RuntimeException;

use function array_keys;
use function sprintf;

/**
 * @psalm-suppress PossiblyUnusedProperty
 */
class RelationAspect
{
    public string $description;
    public bool $factionOnly;
    public string $name;
    public int $page;
    public string $ruleset;

    /**
     * @var array<string, array<string, int|string>>
     */
    public static ?array $aspects;

    public function __construct(public string $id)
    {
        $filename = config('subversion.data_path') . 'relation-aspects.php';
        /** @psalm-suppress UnresolvableInclude */
        self::$aspects ??= require $filename;

        if (!isset(self::$aspects[$id])) {
            throw new RuntimeException(
                sprintf('Relation aspect "%s" not found', $id)
            );
        }

        $aspect = self::$aspects[$id];
        $this->description = $aspect['description'];
        $this->factionOnly = $aspect['faction-only'];
        $this->name = $aspect['name'];
        $this->page = $aspect['page'];
        $this->ruleset = $aspect['ruleset'];
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @return array<int, RelationAspect>
     */
    public static function all(): array
    {
        $filename = config('subversion.data_path') . 'relation-aspects.php';
        /** @psalm-suppress UnresolvableInclude */
        self::$aspects ??= require $filename;

        $aspects = [];
        /** @var string $aspect */
        foreach (array_keys(self::$aspects) as $aspect) {
            $aspects[] = new self($aspect);
        }
        return $aspects;
    }
}
