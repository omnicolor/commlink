<?php

declare(strict_types=1);

namespace App\ValueObjects;

use InvalidArgumentException;
use Override;
use Stringable;

use function filter_var;

use const FILTER_VALIDATE_EMAIL;

readonly class Email implements Stringable
{
    public function __construct(public string $address)
    {
        if (false === filter_var($address, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Email address is not valid');
        }
    }

    #[Override]
    public function __toString(): string
    {
        return $this->address;
    }

    public function is(Email $email): bool
    {
        return $this->address === $email->address;
    }
}
