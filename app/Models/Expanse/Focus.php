<?php

declare(strict_types=1);

namespace App\Models\Expanse;

/**
 * Class representing an Expanse Focus.
 */
class Focus
{
    /**
     * Attributes the Focus is attached to.
     * @var string
     */
    public string $attribute;

    /**
     * Description of the Focus.
     * @var string
     */
    public string $description;

    /**
     * Collection of all focuses.
     * @var ?array<string, array<string, string|int>>
     */
    public static ?array $focuses;

    /**
     * Unique identifier for the focus.
     * @var string
     */
    public string $id;

    /**
     * Name of the Focus.
     * @var string
     */
    public string $name;

    /**
     * Page the focus is listed on.
     * @var int
     */
    public int $page;

    /**
     * Constructor.
     * @param string $id
     * @throws \RuntimeException
     */
    public function __construct(string $id)
    {
        $filename = config('app.data_path.expanse') . 'focuses.php';
        self::$focuses ??= require $filename;

        $id = strtolower($id);
        if (!isset(self::$focuses[$id])) {
            throw new \RuntimeException(
                sprintf('Focus ID "%s" is invalid', $id)
            );
        }

        $focus = self::$focuses[$id];
        $this->attribute = $focus['attribute'];
        $this->description = $focus['description'];
        $this->id = $id;
        $this->name = $focus['name'];
        $this->page = $focus['page'];
    }

    /**
     * Return the name of the Focus.
     * @return string
     */
    public function __toString(): string
    {
        return $this->name;
    }
}
