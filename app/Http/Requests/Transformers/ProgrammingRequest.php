<?php

declare(strict_types=1);

namespace App\Http\Requests\Transformers;

use App\Models\Transformers\Programming;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;

class ProgrammingRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
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
