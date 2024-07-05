<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use RuntimeException;
use Stringable;

use function config;
use function explode;
use function sprintf;
use function strtolower;
use function trim;

/**
 * Class representing a magical tradition in Shadowrun.
 * @psalm-suppress PossiblyUnusedProperty
 */
class Tradition implements Stringable
{
    /**
     * Description of the tradition.
     */
    public string $description;

    /**
     * Attributes used to resist drain.
     */
    public string $drain;

    /**
     * Map of elements to categories.
     * @var array<string, string>
     */
    public array $elements;

    /**
     * Name of the tradition.
     */
    public string $name;

    /**
     * Page the tradition was introduced on.
     */
    public int $page;

    /**
     * Rule book the tradition was introduced in.
     */
    public string $ruleset;

    /**
     * Collection of all traditions.
     * @var ?array<mixed>
     */
    public static ?array $traditions;

    /**
     * @throws RuntimeException if the ID is invalid or not found
     */
    public function __construct(public string $id)
    {
        $filename = config('shadowrun5e.data_path') . 'traditions.php';
        /** @psalm-suppress UnresolvableInclude */
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
     * @psalm-suppress PossiblyUnusedMethod
     * @return array<int, string> Two drain attributes
     */
    public function getDrainAttributes(): array
    {
        $drain = explode('+', $this->drain);
        return [trim($drain[0]), trim($drain[1])];
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
