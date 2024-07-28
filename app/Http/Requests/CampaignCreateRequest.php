<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Avatar\Models\Era;

use function array_keys;
use function config;

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
     * @psalm-suppress PossiblyUnusedMethod
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'avatar-era' => [
                Rule::in(Era::values()),
                'required_if:system,avatar',
            ],
            'avatar-focus' => [
                'required_if:system,avatar',
                Rule::in(['defeat', 'protect', 'change', 'deliver', 'rescue', 'learn']),
            ],
            'avatar-focus-defeat-object' => [
                'required_if:avatar-focus,defeat',
            ],
            'avatar-focus-protect-object' => [
                'required_if:avatar-focus,protect',
            ],
            'avatar-focus-change-object' => [
                'required_if:avatar-focus,change',
            ],
            'avatar-focus-deliver-object' => [
                'required_if:avatar-focus,deliver',
            ],
            'avatar-focus-rescue-object' => [
                'required_if:avatar-focus,rescue',
            ],
            'avatar-focus-learn-object' => [
                'required_if:avatar-focus,learn',
            ],
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
            'subversion-community-description' => [
                'nullable',
                'required_if:system,subversion',
                'string',
            ],
            'subversion-community-type' => [
                'nullable',
                'required_if:system,subversion',
                'string',
            ],
            'system' => [
                'max:30',
                'required',
                Rule::in(array_keys(config('app.systems'))),
            ],
        ];
    }
}
