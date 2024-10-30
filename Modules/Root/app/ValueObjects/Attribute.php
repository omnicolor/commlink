<?php

declare(strict_types=1);

namespace Modules\Root\ValueObjects;

use DomainException;
use Stringable;

readonly class Attribute
{
    public function __construct(public int $value)
    {
        if (-1 > $value) {
            throw new DomainException('Attributes can not be less than -1');
        }
        if (2 < $value) {
            throw new DomainException('Attributes can not be greater than 2');
        }
    }

    public function __toString(): string
    {
        return (string)$this->value;
    }
}
