<?php

declare(strict_types=1);

namespace App\Features;

use Stringable;

class SubversionCharacterGeneration implements Stringable
{
    public function resolve(): bool
    {
        return false;
    }

    public function __toString(): string
    {
        return 'Subversion Character Generation';
    }
}
