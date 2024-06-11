<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5e;

use Stringable;

use function sprintf;

/**
 * Representation of a Shadowrun 5E fake license.
 */
class License implements Stringable
{
    public function __construct(public int $rating, public string $name)
    {
    }

    public function __toString(): string
    {
        return sprintf('%s (%d)', $this->name, $this->rating);
    }

    public function getCost(): int
    {
        return $this->rating * 200;
    }
}
