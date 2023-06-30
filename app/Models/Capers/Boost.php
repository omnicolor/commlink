<?php

declare(strict_types=1);

namespace App\Models\Capers;

/**
 * @psalm-suppress PossiblyUnusedProperty
 */
class Boost
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
