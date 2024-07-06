<?php

declare(strict_types=1);

namespace Modules\Subversion\Models;

/**
 * @property array<string, int> $effects
 * @psalm-suppress PossiblyUnusedProperty
 */
readonly class ImpulseResponse
{
    /**
     * @param array<string, int> $effects
     */
    public function __construct(
        public string $id,
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
