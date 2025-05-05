<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use Override;
use RuntimeException;
use Stringable;

use function config;
use function sprintf;
use function strtolower;

/**
 * Martial art style.
 */
final class MartialArtsStyle implements Stringable
{
    /**
     * Collection of IDs for techniques the style allows.
     * @var array<int, string>
     */
    public array $allowedTechniques;
    public readonly string $description;
    public readonly string $name;
    public readonly int $page;
    public readonly string $ruleset;

    /**
     * Collection of all styles.
     * @var ?array<string, array<string, mixed>>
     */
    public static ?array $styles;

    /**
     * @throws RuntimeException if the ID is invalid
     */
    public function __construct(public readonly string $id)
    {
        $filename = config('shadowrun5e.data_path')
            . 'martial-arts-styles.php';
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

    #[Override]
    public function __toString(): string
    {
        return $this->name;
    }
}
