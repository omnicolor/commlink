<?php

declare(strict_types=1);

namespace App\Features;

use Override;
use Stringable;

class CyberpunkCharacterGeneration implements Stringable
{
    /**
     * Resolve the feature's initial value.
     */
    public function resolve(): bool
    {
        return false;
    }

    #[Override]
    public function __toString(): string
    {
        return 'Cyberpunk Character Generation';
    }
}
