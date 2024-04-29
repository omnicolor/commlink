<?php

declare(strict_types=1);

namespace App\Models\Subversion;

/**
 * @property array<string, int> $effects
 */
readonly class ImpulseDowntime
{
    /**
     * @param array<string, int> $effects
     */
    public function __construct(
        public string $name,
        public string $description,
        public array $effects,
    ) {
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
