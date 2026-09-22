<?php

declare(strict_types=1);

namespace Modules\Battletech\ValueObjects;

use DomainException;
use Override;
use Stringable;

readonly class Attribute implements Stringable
{
    public function __construct(public int $value)
    {
        if (0 >= $value) {
            throw new DomainException('Attributes can not be less than 1.');
        }
    }

    #[Override]
    public function __toString(): string
    {
        return (string)$this->value;
    }
}
