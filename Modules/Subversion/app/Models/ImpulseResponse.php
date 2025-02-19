<?php

declare(strict_types=1);

namespace Modules\Subversion\Models;

use Override;
use Stringable;

/**
 * @property array<string, int> $effects
 */

readonly class ImpulseResponse implements Stringable
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

    #[Override]
    public function __toString(): string
    {
        return $this->name;
    }
}
