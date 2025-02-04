<?php

declare(strict_types=1);

namespace Modules\Avatar\ValueObjects;

use DomainException;
use Stringable;

readonly class AttributeModifier implements Stringable
{
    public function __construct(public int $value)
    {
        if (-1 > $value) {
            throw new DomainException('Attribute modifiers can not be less than -1');
        }
        if (1 < $value) {
            throw new DomainException('Attribute modifiers can not be greater than 1');
        }
    }

    #[Override]
    public function __toString(): string
    {
        return (string)$this->value;
    }
}
