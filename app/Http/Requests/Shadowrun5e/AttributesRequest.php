<?php

declare(strict_types=1);

namespace App\Http\Requests\Shadowrun5e;

use Illuminate\Foundation\Http\FormRequest;

class AttributesRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'agility' => [
                'integer',
                'max:8',
                'min:1',
                'required',
            ],
            'body' => [
                'integer',
                'max:11',
                'min:1',
                'required',
            ],
            'charisma' => [
                'integer',
                'max:9',
                'min:1',
                'required',
            ],
            'edge' => [
                'integer',
                'max:8',
                'min:1',
                'required',
            ],
            'intuition' => [
                'integer',
                'max:7',
                'min:1',
                'required',
            ],
            'logic' => [
                'integer',
                'max:7',
                'min:1',
                'required',
            ],
            'magic' => [
                'integer',
                'max:6',
                'min:1',
                'sometimes',
            ],
            'reaction' => [
                'integer',
                'max:7',
                'min:1',
                'required',
            ],
            'resonance' => [
                'integer',
                'max:6',
                'min:1',
                'sometimes',
            ],
            'strength' => [
                'integer',
                'max:11',
                'min:1',
                'required',
            ],
            'willpower' => [
                'integer',
                'max:7',
                'min:1',
                'required',
            ],
        ];
    }
}
