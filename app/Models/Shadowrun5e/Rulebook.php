<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5e;

use RuntimeException;

class Rulebook
{
    /**
     * Whether the rulebook is included in Commlink character creation by
     * default.
     * @var bool
     */
    public bool $default = true;

    /**
     * Description of the rulebook.
     * @var string
     */
    public string $description;

    /**
     * Unique ID for the rulebook.
     * @var string
     */
    public string $id;

    /**
     * Name of the rulebook.
     * @var string
     */
    public string $name;

    /**
     * Whether the rulebook is required to play the game.
     * @var bool
     */
    public bool $required = false;

    /**
     * List of all rulebooks.
     * @var ?array<string, array<string, bool|string>>
     */
    public static ?array $books;

    /**
     * Construct a new rulebook object.
     * @param string $id ID to load
     * @throws RuntimeException
     */
    public function __construct(string $id)
    {
        $filename = config('app.data_path.shadowrun5e') . 'rulebooks.php';
        self::$books ??= require $filename;

        $id = \strtolower($id);
        if (!isset(self::$books[$id])) {
            throw new RuntimeException(
                \sprintf('Ruleset ID "%s" is invalid', $id)
            );
        }

        $book = self::$books[$id];
        $this->default = $book['default'] ?? true;
        $this->description = $book['description'];
        $this->id = $id;
        $this->name = $book['name'];
        $this->required = $book['required'] ?? false;
    }

    /**
     * Return the name of the rulebook.
     * @return string
     */
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
        $filename = config('app.data_path.shadowrun5e') . 'rulebooks.php';
        self::$books ??= require $filename;

        $books = [];
        // @phpstan-ignore-next-line
        foreach (self::$books as $id => $book) {
            $books[(string)$id] = new Rulebook($id);
        }
        return $books;
    }
}
