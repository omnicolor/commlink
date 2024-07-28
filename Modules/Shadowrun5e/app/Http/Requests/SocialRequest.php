<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Shadowrun5e\Rules\ContactArrayRule;

class SocialRequest extends FormRequest
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
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
