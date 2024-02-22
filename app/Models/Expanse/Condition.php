<?php

declare(strict_types=1);

namespace App\Models\Expanse;

use RuntimeException;

use function sprintf;
use function strtolower;

/**
 * Conditions that can affect characters.
 */
class Condition
{
    /**
     * Collection of all conditions.
     * @var array<string, array<string, int|string>>
     */
    public static ?array $conditions;

    /**
     * Short description of the condition's effects.
     * @psalm-suppress PossiblyUnusedProperty
     */
    public string $description;

    /**
     * Unique ID for the condition.
     * @psalm-suppress PossiblyUnusedProperty
     */
    public string $id;

    /**
     * Name of the condition.
     */
    public string $name;

    /**
     * Page the condition is described on.
     * @psalm-suppress PossiblyUnusedProperty
     */
    public int $page;

    /**
     * Constructor.
     * @psalm-suppress PossiblyUnusedMethod
     * @throws RuntimeException
     */
    public function __construct(string $id)
    {
        $filename = config('app.data_path.expanse') . 'conditions.php';
        self::$conditions ??= require $filename;

        $id = strtolower($id);
        if (!isset(self::$conditions[$id])) {
            throw new RuntimeException(
                sprintf('Condition ID "%s" is invalid', $id)
            );
        }

        $condition = self::$conditions[$id];
        $this->description = $condition['description'];
        $this->id = $id;
        $this->name = $condition['name'];
        $this->page = $condition['page'];
    }

    /**
     * Return the condition's name.
     * @return string
     */
    public function __toString(): string
    {
        return $this->name;
    }
}
