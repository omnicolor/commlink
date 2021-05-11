<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5E;

/**
 * Martial art style.
 */
class MartialArtsStyle
{
    /**
     * Collection of IDs for techniques the style allows.
     * @var string[]
     */
    public array $allowedTechniques;

    /**
     * Description of the style.
     * @var string
     */
    public string $description;

    /**
     * Unique ID for the style.
     * @var string
     */
    public string $id;

    /**
     * Name of the style.
     * @var string
     */
    public string $name;

    /**
     * Page the style was introduced on.
     * @var int
     */
    public int $page;

    /**
     * ID of the book the style was introduced in.
     * @var string
     */
    public string $ruleset;

    /**
     * Collection of all styles.
     * @var ?array<mixed>
     */
    public static ?array $styles;

    /**
     * Construct a new Style object.
     * @param string $id ID to load
     * @throws \RuntimeException if the ID is invalid
     */
    public function __construct(string $id)
    {
        $filename = config('app.data_path.shadowrun5e')
            . 'martial-arts-styles.php';
        self::$styles ??= require $filename;

        $id = \strtolower($id);
        if (!isset(self::$styles[$id])) {
            throw new \RuntimeException(\sprintf(
                'Martial Arts Style ID "%s" is invalid',
                $id
            ));
        }

        $style = self::$styles[$id];
        $this->description = $style['description'];
        $this->id = $id;
        $this->name = $style['name'];
        $this->page = $style['page'];
        $this->ruleset = $style['ruleset'];
        $this->allowedTechniques = $style['techniques'];
    }

    /**
     * Return the style's name.
     * @return string
     */
    public function __toString(): string
    {
        return $this->name;
    }
}
