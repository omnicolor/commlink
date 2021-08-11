<?php

declare(strict_types=1);

namespace App\Http\Requests\CyberpunkRed;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\In;

class LifepathRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @return array<string, array<int, In|string>>
     */
    public function rules(): array
    {
        return [
            'affectation' => [
                'between:1,10',
                'integer',
                'numeric',
                'required',
            ],
            'background' => [
                'between:1,10',
                'integer',
                'numeric',
                'required',
            ],
            'clothing' => [
                'between:1,10',
                'integer',
                'numeric',
                'required',
            ],
            'environment' => [
                'between:1,10',
                'integer',
                'numeric',
                'required',
            ],
            'family-crisis' => [
                'between:1,10',
                'integer',
                'numeric',
                'required',
            ],
            'feeling' => [
                'between:1,10',
                'integer',
                'numeric',
                'required',
            ],
            'hair' => [
                'between:1,10',
                'integer',
                'numeric',
                'required',
            ],
            'origin' => [
                'between:1,10',
                'integer',
                'numeric',
                'required',
            ],
            'person' => [
                'between:1,10',
                'integer',
                'numeric',
                'required',
            ],
            'personality' => [
                'between:1,10',
                'integer',
                'numeric',
                'required',
            ],
            'possession' => [
                'between:1,10',
                'integer',
                'numeric',
                'required',
            ],
            'value' => [
                'between:1,10',
                'integer',
                'numeric',
                'required',
            ],
        ];
    }
}
