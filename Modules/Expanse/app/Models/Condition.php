<?php

declare(strict_types=1);

namespace Modules\Expanse\Models;

use Override;
use RuntimeException;
use Stringable;

use function config;
use function sprintf;
use function strtolower;

/**
 * Conditions that can affect characters.
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
     */
    public string $description;

    /**
     * Name of the condition.
     */
    public string $name;

    /**
     * Page the condition is described on.
     */
    public int $page;

    /**
     * Constructor.
     * @throws RuntimeException
     */
    public function __construct(public string $id)
    {
        $filename = config('expanse.data_path') . 'conditions.php';
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

    #[Override]
    public function __toString(): string
    {
        return $this->name;
    }
}
