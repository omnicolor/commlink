<?php

declare(strict_types=1);

namespace Modules\Alien\Rules;

use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Modules\Alien\Models\Career;

class OneFromRow implements DataAwareRule, ValidationRule
{
    /**
     * @var array<string, mixed>
     */
    protected $data = [];

    public function __construct(protected Career $career)
    {
    }

    /**
     * Set the data under validation.
     * @param array<mixed> $data
     */
    public function setData(array $data): static
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $gear = $this->career->gear;
        foreach ($gear as $row) {
            $row = collect($row)->pluck('id');
            if (
                $row->contains($this->data['gear'][0] ?? '')
                && $row->contains($this->data['gear'][1] ?? '')
            ) {
                $fail('You cannot choose two items from the same row.');
            }
        }
    }
}
