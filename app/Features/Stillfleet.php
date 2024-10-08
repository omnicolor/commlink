<?php

declare(strict_types=1);

namespace App\Features;

use Stringable;

/**
 * @psalm-suppress UnusedClass
 */
class Stillfleet implements Stringable
{
    /**
     * Resolve the feature's initial value.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function resolve(): false
    {
        return false;
    }

    public function __toString(): string
    {
        return 'Stillfleet RPG';
    }
}
