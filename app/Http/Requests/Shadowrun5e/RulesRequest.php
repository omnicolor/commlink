<?php

declare(strict_types=1);

namespace App\Http\Requests\Shadowrun5e;

use App\Models\Shadowrun5E\Rulebook;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;

class RulesRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @return array<string, array<int, string|Closure|In>>
     */
    public function rules(): array
    {
        $books = array_keys(Rulebook::all());
        return [
            'gameplay' => [
                'filled',
                'required',
                Rule::in(['street', 'established', 'prime']),
            ],
            'nav' => [
                'in:next,prev',
                'required',
            ],
            'rulebook' => [
                'array',
                'required',
                Rule::in($books),
                function (string $attribute, array $value, Closure $fail): void {
                    if (!in_array('core', $value, true)) {
                        $fail('The core rulebook is required.');
                    }
                },
            ],
            'start-date' => [
                'date',
                'nullable',
            ],
            // Character creation system, not game system.
            'system' => [
                'filled',
                'required',
                Rule::in(['priority', 'sum-to-ten']),
            ],
        ];
    }
}
