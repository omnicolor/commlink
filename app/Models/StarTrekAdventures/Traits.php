<?php

declare(strict_types=1);

namespace App\Models\StarTrekAdventures;

use RuntimeException;

/**
 * Class representing a species' trait.
 *
 * Plural since trait is a PHP reserved-word.
 * @psalm-suppress PossiblyUnusedProperty
 */
class Traits
{
    /**
     * Description of the trait.
     * @var string
     */
    public string $description;

    /**
     * ID of the trait.
     * @var string
     */
    public string $id;

    /**
     * Name of the trait.
     * @var string
     */
    public string $name;

    /**
     * Page the talent was mentioned on.
     * @var int
     */
    public int $page;

    /**
     * Book the trait was mentioned in.
     * @var string
     */
    public string $ruleset;

    /**
     * Collect of all traits.
     * @var ?array<string, array<string, int|string>>
     */
    public static ?array $traits;

    public function __construct(string $id)
    {
        $filename = config('app.data_path.star-trek-adventures')
            . 'traits.php';
        self::$traits ??= require $filename;

        $id = \strtolower($id);
        if (!isset(self::$traits[$id])) {
            throw new RuntimeException(
                \sprintf('Trait ID "%s" is invalid', $id)
            );
        }

        $trait = self::$traits[$id];
        $this->description = $trait['description'];
        $this->id = $id;
        $this->name = $trait['name'];
        $this->page = (int)$trait['page'];
        $this->ruleset = $trait['ruleset'];
    }

    /**
     * Return the trait as a string.
     * @return string
     */
    public function __toString(): string
    {
        return $this->name;
    }
}
