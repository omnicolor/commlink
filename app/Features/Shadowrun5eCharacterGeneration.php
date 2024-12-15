<?php

declare(strict_types=1);

namespace App\Features;

use Stringable;

class Shadowrun5eCharacterGeneration implements Stringable
{
    /**
     * Resolve the feature's initial value.
     */
    public function resolve(): bool
    {
        return false;
    }

    public function __toString(): string
    {
        return 'Shadowrun 5E Character Generation';
    }
}
