<?php

declare(strict_types=1);

namespace Modules\Dnd5e\ValueObjects;

use OutOfRangeException;
use Override;
use Stringable;

/**
 * @property-read int $modifier
 */
readonly class AbilityValue implements Stringable
{
    public function __construct(public int $value)
    {
        if (1 > $value || 30 < $value) {
            throw new OutOfRangeException('Attribute value is out of range');
        }
    }

    public function __get(string $name): ?int
    {
        if ('modifier' === $name) {
            return -5 + (int)floor($this->value / 2);
        }

        return null;
    }

    #[Override]
    public function __toString(): string
    {
        return (string)$this->value;
    }
}
