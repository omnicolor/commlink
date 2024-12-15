<?php

declare(strict_types=1);

namespace App\Features;

use Stringable;

class TransformersCharacterGeneration implements Stringable
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
        return 'Transformers RPG Character Generation';
    }
}
