<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5e;

/**
 * Representation of a Shadowrun 5E fake license.
 */
class License
{
    /**
     * Constructor.
     * @param int $rating
     * @param string $name
     */
    public function __construct(public int $rating, public string $name)
    {
    }

    /**
     * Return the license as a string.
     * @return string
     */
    public function __toString(): string
    {
        return sprintf('%s (%d)', $this->name, $this->rating);
    }

    /**
     * Return the cost of the license.
     * @return int
     */
    public function getCost(): int
    {
        return $this->rating * 200;
    }
}
