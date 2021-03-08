<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5E;

/**
 * Class representing a magical tradition in Shadowrun.
 */
class Tradition
{
    /**
     * Description of the tradition.
     * @var string
     */
    public string $description;

    /**
     * Attributes used to resist drain.
     * @var string
     */
    public string $drain;

    /**
     * Map of elements to categories.
     * @var array<string, string>
     */
    public array $elements;

    /**
     * Unique ID for the tradition.
     * @var string
     */
    public string $id;

    /**
     * Name of the tradition.
     * @var string
     */
    public string $name;

    /**
     * Page the tradition was introduced on.
     * @var int
     */
    public int $page;

    /**
     * Rule book the tradition was introduced in.
     * @var string
     */
    public string $ruleset;

    /**
     * Collection of all traditions.
     * @var ?array<mixed>
     */
    public static ?array $traditions;

    /**
     * Construct a new Tradition object.
     * @param string $identifier ID of the tradition
     * @throws \RuntimeException if the ID is invalid or not found
     */
    public function __construct(string $identifier)
    {
        $filename = config('app.data_path.shadowrun5e') . 'traditions.php';
        self::$traditions ??= require $filename;

        $identifier = strtolower($identifier);
        if (!isset(self::$traditions[$identifier])) {
            throw new \RuntimeException(sprintf(
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
     * @return string[] Two drain attributes
     */
    public function getDrainAttributes(): array
    {
        $drain = explode('+', $this->drain);
        return [trim($drain[0]), trim($drain[1])];
    }

    /**
     * Return the name of the tradition.
     * @return string
     */
    public function __toString(): string
    {
        return $this->name;
    }
}
