<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use RuntimeException;
use Stringable;

use function config;
use function sprintf;
use function strtolower;

/**
 * Martial art style.
 * @psalm-suppress PossiblyUnusedProperty
 */
class MartialArtsStyle implements Stringable
{
    /**
     * Collection of IDs for techniques the style allows.
     * @var array<int, string>
     */
    public array $allowedTechniques;

    /**
     * Description of the style.
     */
    public string $description;

    /**
     * Name of the style.
     */
    public string $name;

    /**
     * Page the style was introduced on.
     */
    public int $page;

    /**
     * ID of the book the style was introduced in.
     */
    public string $ruleset;

    /**
     * Collection of all styles.
     * @var ?array<string, array<string, mixed>>
     */
    public static ?array $styles;

    /**
     * @throws RuntimeException if the ID is invalid
     */
    public function __construct(public string $id)
    {
        $filename = config('shadowrun5e.data_path')
            . 'martial-arts-styles.php';
        /** @psalm-suppress UnresolvableInclude */
        self::$styles ??= require $filename;

        $id = strtolower($id);
        if (!isset(self::$styles[$id])) {
            throw new RuntimeException(sprintf(
                'Martial Arts Style ID "%s" is invalid',
                $id
            ));
        }

        $style = self::$styles[$id];
        $this->description = $style['description'];
        $this->name = $style['name'];
        $this->page = $style['page'];
        $this->ruleset = $style['ruleset'];
        $this->allowedTechniques = $style['techniques'];
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
