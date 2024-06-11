<?php

declare(strict_types=1);

namespace App\Models\Capers;

use Stringable;

/**
 * @psalm-suppress PossiblyUnusedProperty
 */
class Boost implements Stringable
{
    public function __construct(
        public string $id,
        public string $description,
        public string $name
    ) {
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
