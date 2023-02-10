<?php

declare(strict_types=1);

namespace App\Models\StarTrekAdventures;

/**
 * Species object for Star Trek Adventures.
 */
abstract class Species
{
    /**
     * Attribute modifiers for the species.
     * @var array<string, int>
     */
    public array $attributes;

    /**
     * Description of the species.
     * @var string
     */
    public string $description;

    /**
     * Name of the species.
     * @var string
     */
    public string $name;

    /**
     * Collection of talents the character receives access to.
     * @var TalentArray
     */
    public TalentArray $talents;

    /**
     * Trait for the species.
     * @var Traits
     */
    public Traits $trait;

    /**
     * Return the species as a string (the name).
     * @return string
     */
    public function __toString(): string
    {
        return $this->name;
    }

    public static function find(string $species): Species
    {
        $class = \sprintf('App\Models\StarTrekAdventures\Species\%s', $species);
        /** @var Species */
        $object = new $class();
        return $object;
    }
}
