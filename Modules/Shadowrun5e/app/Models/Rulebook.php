<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use Override;
use RuntimeException;
use Stringable;

use function array_keys;
use function config;
use function sprintf;
use function strtolower;

final class Rulebook implements Stringable
{
    /**
     * Whether the rulebook is included in Commlink character creation by
     * default.
     */
    public readonly bool $default;
    public readonly string $description;
    public readonly string $name;

    /**
     * Whether the rulebook is required to play the game.
     */
    public readonly bool $required;

    /**
     * List of all rulebooks.
     * @var ?array<string, array<string, bool|string>>
     */
    public static ?array $books;

    /**
     * @throws RuntimeException
     */
    public function __construct(public readonly string $id)
    {
        $filename = config('shadowrun5e.data_path') . 'rulebooks.php';
        self::$books ??= require $filename;

        $id = strtolower($id);
        if (!isset(self::$books[$id])) {
            throw new RuntimeException(
                sprintf('Ruleset ID "%s" is invalid', $id)
            );
        }

        $book = self::$books[$id];
        $this->default = $book['default'] ?? true;
        $this->description = $book['description'];
        $this->name = $book['name'];
        $this->required = $book['required'] ?? false;
    }

    #[Override]
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * Return all rulebooks.
     * @return array<string, Rulebook>
     */
    public static function all(): array
    {
        $filename = config('shadowrun5e.data_path') . 'rulebooks.php';
        self::$books ??= require $filename;

        $books = [];
        /** @var string $id */
        foreach (array_keys(self::$books ?? []) as $id) {
            $books[$id] = new Rulebook($id);
        }
        return $books;
    }
}
