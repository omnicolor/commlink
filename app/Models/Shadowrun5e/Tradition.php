<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5e;

/**
 * Class representing a magical tradition in Shadowrun.
 * @psalm-suppress PossiblyUnusedProperty
 */
class Tradition
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
     * Unique ID for the tradition.
     */
    public string $id;

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
     * Construct a new Tradition object.
     * @throws \RuntimeException if the ID is invalid or not found
     */
    public function __construct(string $identifier)
    {
        $filename = config('app.data_path.shadowrun5e') . 'traditions.php';
        self::$traditions ??= require $filename;

        $identifier = \strtolower($identifier);
        if (!isset(self::$traditions[$identifier])) {
            throw new \RuntimeException(\sprintf(
                'Tradition ID "%s" not found',
                $identifier
            ));
        }

        $tradition = self::$traditions[$identifier];
        $this->description = $tradition['description'];
        $this->drain = $tradition['drain'];
        $this->elements = $tradition['elements'];
        $this->id = $identifier;
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
        $drain = \explode('+', $this->drain);
        return [\trim($drain[0]), \trim($drain[1])];
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
