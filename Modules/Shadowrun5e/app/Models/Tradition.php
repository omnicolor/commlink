<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use Override;
use RuntimeException;
use Stringable;

use function config;
use function explode;
use function sprintf;
use function strtolower;
use function trim;

/**
 * Class representing a magical tradition in Shadowrun.
 */
class Tradition implements Stringable
{
    public readonly string $description;

    /**
     * Attributes used to resist drain.
     */
    public readonly string $drain;

    /**
     * Map of elements to categories.
     * @var array<string, string>
     */
    public array $elements;
    public readonly string $name;
    public readonly int $page;
    public readonly string $ruleset;

    /**
     * Collection of all traditions.
     * @var ?array<mixed>
     */
    public static ?array $traditions;

    /**
     * @throws RuntimeException if the ID is invalid or not found
     */
    public function __construct(public readonly string $id)
    {
        $filename = config('shadowrun5e.data_path') . 'traditions.php';
        self::$traditions ??= require $filename;

        $id = strtolower($id);
        if (!isset(self::$traditions[$id])) {
            throw new RuntimeException(sprintf(
                'Tradition ID "%s" not found',
                $id
            ));
        }

        $tradition = self::$traditions[$id];
        $this->description = $tradition['description'];
        $this->drain = $tradition['drain'];
        $this->elements = $tradition['elements'];
        $this->name = $tradition['name'];
        $this->page = $tradition['page'] ?? null;
        $this->ruleset = $tradition['ruleset'];
    }

    /**
     * Return an array with the two attributes the tradition uses to resist
     * drain.
     * @return array<int, string> Two drain attributes
     */
    public function getDrainAttributes(): array
    {
        $drain = explode('+', $this->drain);
        return [trim($drain[0]), trim($drain[1])];
    }

    #[Override]
    public function __toString(): string
    {
        return $this->name;
    }
}
