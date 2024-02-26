<?php

declare(strict_types=1);

namespace App\Models\Irc;

/**
 * @psalm-suppress PossiblyUnusedProperty
 */
class User
{
    public function __construct(
        public string $nick,
        public bool $op = false,
        public bool $voice = false,
        public bool $registered = false,
        public bool $linked = false,
    ) {
    }
}
