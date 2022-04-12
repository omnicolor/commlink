<?php

declare(strict_types=1);

namespace App\Http\Requests\Capers;

use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;

abstract class BaseRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to all Capers chargen requests.
     * @return array<string, array<int, string|Closure|In|Rule>>
     */
    public function rules(): array
    {
        return [
            'nav' => [
                'required',
                'string',
                Rule::in([
                    'anchors',
                    'basics',
                    'boosts',
                    'gear',
                    'review',
                    'save',
                    'skills',
                    'traits',
                ]),
            ],
        ];
    }
}
