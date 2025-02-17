<?php

declare(strict_types=1);

namespace Modules\Transformers\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;
use Modules\Transformers\Enums\Programming;

class ProgrammingRequest extends FormRequest
{
    /**
     * @return array<string, array<int, string|In>>
     */
    public function rules(): array
    {
        return [
            'programming' => [
                'required',
                Rule::in(array_column(Programming::cases(), 'value')),
            ],
        ];
    }
}
