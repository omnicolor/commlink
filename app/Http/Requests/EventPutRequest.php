<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventPutRequest extends FormRequest
{
    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'description' => [
                'nullable',
                'string',
            ],
            'game_end' => [
                'nullable',
                'string',
            ],
            'game_start' => [
                'nullable',
                'string',
            ],
            'name' => [
                'required',
                'string',
            ],
            'real_end' => [
                'after:real_start',
                'date',
                'nullable',
            ],
            'real_start' => [
                'date',
                'required',
            ],
        ];
    }
}
