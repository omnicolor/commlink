<?php

declare(strict_types=1);

namespace App\Models\Expanse;

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
     * @var string
     */
    public string $description;

    /**
     * Unique ID for the condition.
     * @var string
     */
    public string $id;

    /**
     * Name of the condition.
     * @var string
     */
    public string $name;

    /**
     * Page the condition is described on.
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
        $filename = config('app.data_path.expanse') . 'conditions.php';
        self::$conditions ??= require $filename;

        $id = \strtolower($id);
        if (!isset(self::$conditions[$id])) {
            throw new \RuntimeException(
                \sprintf('Condition ID "%s" is invalid', $id)
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
