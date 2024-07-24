<?php

declare(strict_types=1);

namespace App\Rules\Shadowrun5e;

use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * A Shadowrun contact is added in pieces, with the front-end sending five
 * arrays, four of which are required. This makes sure that the four required
 * arrays have the same number of values.
 */
class ContactArrayRule implements DataAwareRule, ValidationRule
{
    /**
     * @var array<string, mixed>
     */
    protected $data = [];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $data = $this->data;
        $expected = count($data['contact-names'] ?? []);
        if (
            count($data['contact-archetypes'] ?? []) !== $expected
            || count($data['contact-connections'] ?? []) !== $expected
            || count($data['contact-loyalties'] ?? []) !== $expected
        ) {
            $fail('One or more contacts are missing some data');
        }
    }

    /**
     * Set the data under validation.
     * @phpstan-ignore-next-line
     * @psalm-suppress MoreSpecificImplementedParamType
     * @param array<string, mixed> $data
     */
    public function setData(array $data): static
    {
        $this->data = $data;
        return $this;
    }
}
