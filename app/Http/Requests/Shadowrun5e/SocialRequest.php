<?php

declare(strict_types=1);

namespace App\Http\Requests\Shadowrun5e;

use App\Rules\Shadowrun5e\ContactArrayRule;
use Illuminate\Foundation\Http\FormRequest;

class SocialRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @return array<string, array<int, ContactArrayRule|string>>
     */
    public function rules(): array
    {
        return [
            'contact-archetypes.*' => [
                'required_with:contact-names.*',
                'string',
            ],
            'contact-connections.*' => [
                'integer',
                'max:12',
                'min:1',
            ],
            'contact-loyalties.*' => [
                'integer',
                'max:6',
                'min:1',
            ],
            'contact-names.*' => [
                new ContactArrayRule(),
                'string',
            ],
            'contact-notes.*' => [
                'nullable',
                'string',
            ],
            'nav' => [
                'in:next,prev',
                'required',
            ],
        ];
    }
}
