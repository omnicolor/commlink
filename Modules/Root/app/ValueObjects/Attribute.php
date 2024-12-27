<?php

declare(strict_types=1);

namespace Modules\Root\ValueObjects;

use DomainException;
use Stringable;

readonly class Attribute implements Stringable
{
    public function __construct(public int $value, bool $improved_by_move = false)
    {
        if (-1 > $value) {
            throw new DomainException('Attributes can not be less than -1');
        }
        if (2 < $value && !$improved_by_move) {
            throw new DomainException('Attributes can not be greater than 2');
        }
        if (3 < $value) {
            throw new DomainException('Attributes can not be greater than 3 including a move');
        }
    }

    public function __toString(): string
    {
        return (string)$this->value;
    }
}
