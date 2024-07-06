<?php

declare(strict_types=1);

namespace Modules\Startrekadventures\Models;

use Stringable;

use function sprintf;

/**
 * Species object for Star Trek Adventures.
 * @psalm-suppress PossiblyUnusedProperty
 */
abstract class Species implements Stringable
{
    /**
     * Attribute modifiers for the species.
     * @var array<string, int>
     */
    public array $attributes;

    /**
     * Description of the species.
     */
    public string $description;

    /**
     * Name of the species.
     */
    public string $name;

    /**
     * Collection of talents the character receives access to.
     */
    public TalentArray $talents;

    /**
     * Trait for the species.
     */
    public Traits $trait;

    /**
     * Return the species as a string (the name).
     */
    public function __toString(): string
    {
        return $this->name;
    }

    public static function find(string $species): Species
    {
        $class = sprintf('Modules\Startrekadventures\Models\Species\%s', $species);
        /** @var Species */
        $object = new $class();
        return $object;
    }
}
