<?php

declare(strict_types=1);

namespace App\Features;

use Override;
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

    #[Override]
    public function __toString(): string
    {
        return 'Hero Lab Import';
    }
}
