<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BackgroundRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @psalm-suppress PossiblyUnusedMethod
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'age' => [
                'nullable',
                'sometimes',
                'string',
            ],
            'appearance' => [
                'nullable',
                'sometimes',
                'string',
            ],
            'born' => [
                'nullable',
                'sometimes',
                'string',
            ],
            'description' => [
                'nullable',
                'sometimes',
                'string',
            ],
            'education' => [
                'nullable',
                'sometimes',
                'string',
            ],
            'family' => [
                'nullable',
                'sometimes',
                'string',
            ],
            'gender' => [
                'nullable',
                'sometimes',
                'string',
            ],
            'goals' => [
                'nullable',
                'sometimes',
                'string',
            ],
            'hate' => [
                'nullable',
                'sometimes',
                'string',
            ],
            'limitations' => [
                'nullable',
                'sometimes',
                'string',
            ],
            'living' => [
                'nullable',
                'sometimes',
                'string',
            ],
            'love' => [
                'nullable',
                'sometimes',
                'string',
            ],
            'married' => [
                'nullable',
                'sometimes',
                'string',
            ],
            'moral' => [
                'nullable',
                'sometimes',
                'string',
            ],
            'motivation' => [
                'nullable',
                'sometimes',
                'string',
            ],
            'name' => [
                'nullable',
                'sometimes',
                'string',
            ],
            'nav' => [
                'required',
                'string',
            ],
            'personality' => [
                'nullable',
                'sometimes',
                'string',
            ],
            'qualities' => [
                'nullable',
                'sometimes',
                'string',
            ],
            'religion' => [
                'nullable',
                'sometimes',
                'string',
            ],
            'size' => [
                'nullable',
                'sometimes',
                'string',
            ],
            'why' => [
                'nullable',
                'sometimes',
                'string',
            ],
        ];
    }
}
