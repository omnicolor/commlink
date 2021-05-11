<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CampaignCreateRequest extends FormRequest
{
    /**
     * Customize the error messages.
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'sr5e-creation.*.in' => 'One of the Shadowrun 5E creation systems is invalid',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'description' => [
                'max:255',
                'nullable',
            ],
            'name' => [
                'filled',
                'max:100',
                'min:1',
                'required',
            ],
            'sr5e-creation.*' => [
                'in:priority,sum-to-ten,karma,life',
                'required_if:system,shadowrun5e',
            ],
            'sr5e-start-date' => [
                'date_format:Y-m-d',
                'max:10',
                'nullable',
            ],
            'system' => [
                'max:30',
                'required',
                Rule::in(\array_keys(config('app.systems'))),
            ],
        ];
    }
}
