<?php

declare(strict_types=1);

namespace App\Features;

use Override;
use Stringable;

class ApiAccess implements Stringable
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
        return 'API Access';
    }
}
