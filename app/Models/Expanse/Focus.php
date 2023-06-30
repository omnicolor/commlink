<?php

declare(strict_types=1);

namespace App\Models\Expanse;

use RuntimeException;

/**
 * Class representing an Expanse Focus.
 * @psalm-suppress PossiblyUnusedProperty
 */
class Focus
{
    /**
     * Attributes the Focus is attached to.
     */
    public string $attribute;

    /**
     * Description of the Focus.
     */
    public string $description;

    /**
     * Collection of all focuses.
     * @var ?array<string, array<string, string|int>>
     */
    public static ?array $focuses;

    /**
     * Unique identifier for the focus.
     */
    public string $id;

    /**
     * Name of the Focus.
     */
    public string $name;

    /**
     * Page the focus is listed on.
     */
    public int $page;

    /**
     * Constructor.
     * @throws RuntimeException
     */
    public function __construct(
        string $id,
        public int $level = 1
    ) {
        $filename = config('app.data_path.expanse') . 'focuses.php';
        self::$focuses ??= require $filename;

        $id = \strtolower($id);
        if (!isset(self::$focuses[$id])) {
            throw new RuntimeException(
                \sprintf('Focus ID "%s" is invalid', $id)
            );
        }

        $focus = self::$focuses[$id];
        $this->attribute = $focus['attribute'];
        $this->description = $focus['description'];
        $this->id = $id;
        $this->name = $focus['name'];
        $this->page = $focus['page'];
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
