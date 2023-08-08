<?php

declare(strict_types=1);

namespace App\Http\Requests\Transformers;

use Illuminate\Foundation\Http\FormRequest;

class BaseRequest extends FormRequest
{
    /**
     * Get the error messages for the defined validation rules.
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'allegiance.required' => 'Your allegiance is required.',
            'color_primar.required' => 'Your primary color is required.',
            'color_secondary.required' => 'Your secondary color is required.',
            'name.required' => 'Your transformer\'s name is required.',
            'quote.required' => 'Your transformer\'s quote is required.',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'allegiance' => [
                'in:Autobots,Decepticons',
                'required',
            ],
            'color_primary' => [
                'required',
                'string',
            ],
            'color_secondary' => [
                'required',
                'string',
            ],
            'name' => [
                'required',
                'string',
            ],
            'quote' => [
                'required',
                'string',
            ],
        ];
    }
}
