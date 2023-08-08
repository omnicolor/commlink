<?php

declare(strict_types=1);

namespace App\Http\Requests\Transformers;

use App\Models\Transformers\Programming;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProgrammingRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @return array<string, array<string|Rule\In>>
     */
    public function rules(): array
    {
        return [
            'programming' => [
                'required',
                Rule::In(array_column(Programming::cases(), 'value')),
            ],
        ];
    }
}
