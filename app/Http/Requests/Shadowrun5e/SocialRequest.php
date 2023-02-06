<?php

declare(strict_types=1);

namespace App\Http\Requests\Shadowrun5e;

use Illuminate\Foundation\Http\FormRequest;

class SocialRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'contact-archetypes' => [
            ],
            'contact-names' => [
            ],
            'nav' => [
                'in:next,prev',
                'required',
            ],
        ];
    }
}
