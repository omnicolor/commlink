<?php

declare(strict_types=1);

namespace Modules\Subversion\Models;

use Override;
use RuntimeException;
use Stringable;

use function array_keys;
use function config;
use function sprintf;

class RelationArchetype implements Stringable
{
    /**
     * @var array<int, array{cost: int, description: string}>
     */
    public array $asks;

    public string $description;
    public bool $faction_only;
    public bool $has_additional;
    public string $name;
    public int $page;
    public string $ruleset;

    /**
     * @var array<string, array<string, int|string>>
     */
    public static ?array $archetypes;

    public function __construct(
        public string $id,
        public ?string $additional = null,
    ) {
        $filename = config('subversion.data_path')
            . 'relation-archetypes.php';
        self::$archetypes ??= require $filename;

        if (!isset(self::$archetypes[$id])) {
            throw new RuntimeException(
                sprintf('Relation archetype "%s" not found', $id)
            );
        }

        $archetype = self::$archetypes[$id];
        $this->asks = $archetype['asks'];
        $this->description = $archetype['description'];
        $this->faction_only = $archetype['faction-only'];
        $this->has_additional = $archetype['has-additional'];
        $this->name = $archetype['name'];
        $this->page = $archetype['page'];
        $this->ruleset = $archetype['ruleset'];
    }

    #[Override]
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @return array<int, RelationArchetype>
     */
    public static function all(): array
    {
        $filename = config('subversion.data_path')
            . 'relation-archetypes.php';
        self::$archetypes ??= require $filename;

        $archetypes = [];
        /** @var string $archetype */
        foreach (array_keys(self::$archetypes ?? []) as $archetype) {
            $archetypes[] = new self($archetype);
        }
        return $archetypes;
    }
}
