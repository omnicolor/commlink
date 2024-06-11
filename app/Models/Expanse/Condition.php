<?php

declare(strict_types=1);

namespace App\Models\Expanse;

use RuntimeException;
use Stringable;

use function config;
use function sprintf;
use function strtolower;

/**
 * Conditions that can affect characters.
 * @psalm-suppress PossiblyUnusedProperty
 */
class Condition implements Stringable
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
    public function __construct(public string $id)
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
        $this->name = $condition['name'];
        $this->page = $condition['page'];
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
