<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InitiativeCreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'base_initiative' => [
                'integer',
                'required_with:initiative_dice',
                'required_without:initiative',
            ],
            'character_name' => [
                'max:30',
                'string',
                'required',
            ],
            'initiative' => [
                'integer',
                'required_without_all:base_initiative,initiative_dice',
            ],
            'initiative_dice' => [
                'integer',
                'max:5',
                'required_with:initiative_dice',
                'required_without:initiative',
            ],
            'grunt_id' => [
                'max:100',
                'nullable',
                'string',
            ],
        ];
    }
}
