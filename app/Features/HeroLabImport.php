<?php

declare(strict_types=1);

namespace App\Features;

use Stringable;

class HeroLabImport implements Stringable
{
    /**
     * Resolve the feature's initial value.
     */
    public function resolve(): false
    {
        return false;
    }

    public function __toString(): string
    {
        return 'Hero Lab Import';
    }
}
