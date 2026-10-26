<?php

declare(strict_types=1);

namespace Modules\Dnd5e\ValueObjects;

use OutOfRangeException;
use Override;
use Stringable;

/**
 * Level a character has achieved in a class (for characters that have
 * multiclassed).
 */
readonly class ClassLevel implements Stringable
{
    public function __construct(public int $level)
    {
        if (1 > $level || 20 < $level) {
            throw new OutOfRangeException('Level must be between 1 and 20');
        }
    }

    #[Override]
    public function __toString(): string
    {
        return (string)$this->level;
    }
}
