<?php

declare(strict_types=1);

namespace App\Http\Requests\Shadowrun5e;

use Illuminate\Foundation\Http\FormRequest;

class VitalsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'birthdate' => [
                'date',
                'nullable',
                'sometimes',
            ],
            'birthplace' => [
                'nullable',
                'sometimes',
                'string',
            ],
            'eyes' => [
                'nullable',
                'sometimes',
                'string',
            ],
            'gender' => [
                'in:male,female,other',
                'sometimes',
                'string',
            ],
            'handle' => [
                'required',
                'string',
            ],
            'hair' => [
                'nullable',
                'sometimes',
                'string',
            ],
            'height' => [
                'nullable',
                'numeric',
                'sometimes',
            ],
            'real-name' => [
                'nullable',
                'sometimes',
                'string',
            ],
            'weight' => [
                'nullable',
                'numeric',
                'sometimes',
            ],
        ];
    }
}
