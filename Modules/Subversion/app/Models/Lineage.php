<?php

declare(strict_types=1);

namespace Modules\Subversion\Models;

use RuntimeException;

use function sprintf;

/**
 * @psalm-suppress PossiblyUnusedProperty
 */
class Lineage
{
    public string $description;
    public string $name;
    public ?LineageOption $option = null;
    public int $page;
    public string $ruleset;

    /**
     * @var array<string, array<string, int|string>>
     */
    public static ?array $lineages;

    /**
     * @var array<string, LineageOption>
     */
    public array $options = [];

    public function __construct(public string $id, ?string $option = null)
    {
        $filename = config('subversion.data_path') . 'lineages.php';
        /** @psalm-suppress UnresolvableInclude */
        self::$lineages ??= require $filename;

        if (!isset(self::$lineages[$id])) {
            throw new RuntimeException(sprintf('Lineage "%s" not found', $id));
        }

        $lineage = self::$lineages[$id];
        $this->description = $lineage['description'];
        $this->name = $lineage['name'];
        $this->page = $lineage['page'];
        $this->ruleset = $lineage['ruleset'];

        foreach ($lineage['options'] as $lineageOption) {
            $this->options[(string)$lineageOption['id']] = new LineageOption(
                $lineageOption['id'],
                $lineageOption['name'],
                $lineageOption['description'],
            );
        }
        if (null !== $option) {
            $this->option = $this->options[$option];
        }
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     * @return array<int, Lineage>
     */
    public static function all(): array
    {
        $filename = config('subversion.data_path') . 'lineages.php';
        /** @psalm-suppress UnresolvableInclude */
        self::$lineages ??= require $filename;

        $lineages = [];
        foreach (self::$lineages as $lineage) {
            $lineages[] = new Lineage($lineage['id']);
        }
        return $lineages;
    }
}
