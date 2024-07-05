<?php

declare(strict_types=1);

namespace Modules\Startrekadventures\Models;

use RuntimeException;
use Stringable;

use function config;
use function sprintf;
use function strtolower;

/**
 * Class representing a species' trait.
 *
 * Plural since trait is a PHP reserved-word.
 * @psalm-suppress PossiblyUnusedProperty
 */
class Traits implements Stringable
{
    public string $description;
    public string $name;
    public int $page;
    public string $ruleset;

    /**
     * Collect of all traits.
     * @var array<string, array<string, int|string>>
     */
    public static ?array $traits = null;

    public function __construct(public string $id)
    {
        $filename = config('startrekadventures.data_path') . 'traits.php';
        /** @psalm-suppress UnresolvableInclude */
        self::$traits ??= require $filename;

        $id = strtolower($id);
        if (!isset(self::$traits[$id])) {
            throw new RuntimeException(
                sprintf('Trait ID "%s" is invalid', $id)
            );
        }

        $trait = self::$traits[$id];
        $this->description = $trait['description'];
        $this->name = $trait['name'];
        $this->page = (int)$trait['page'];
        $this->ruleset = $trait['ruleset'];
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
