<?php

declare(strict_types=1);

namespace Modules\Cyberpunkred\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StatsRequest extends FormRequest
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'body' => [
                'between:2,8',
                'integer',
                'numeric',
                'required',
            ],
            'cool' => [
                'between:2,8',
                'integer',
                'numeric',
                'required',
            ],
            'dexterity' => [
                'between:2,8',
                'integer',
                'numeric',
                'required',
            ],
            'empathy' => [
                'between:2,8',
                'integer',
                'numeric',
                'required',
            ],
            'intelligence' => [
                'between:2,8',
                'integer',
                'numeric',
                'required',
            ],
            'luck' => [
                'between:2,8',
                'integer',
                'numeric',
                'required',
            ],
            'movement' => [
                'between:2,8',
                'integer',
                'numeric',
                'required',
            ],
            'reflexes' => [
                'between:2,8',
                'integer',
                'numeric',
                'required',
            ],
            'technique' => [
                'between:2,8',
                'integer',
                'numeric',
                'required',
            ],
            'willpower' => [
                'between:2,8',
                'integer',
                'numeric',
                'required',
            ],
        ];
    }
}
