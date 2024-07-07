<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

use function collect;
use function explode;

class EachIsAValid implements ValidationRule
{
    public function __construct(protected string $classname)
    {
    }

    /**
     * Determine if the validation rule passes.
     * @param mixed $value
     */
    public function validate($attribute, $value, Closure $fail): void
    {
        /** @var array<int, mixed> */
        $items = $this->classname::all();
        $validValues = collect($items)->pluck('id');
        foreach (explode(',', $value) as $item) {
            if ('' === $item) {
                continue;
            }
            if ($validValues->doesntContain($item)) {
                $fail('Value contains one or more invalid items.');
            }
        }
    }
}
