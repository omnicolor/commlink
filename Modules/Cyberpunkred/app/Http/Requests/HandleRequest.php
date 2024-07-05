<?php

declare(strict_types=1);

namespace Modules\Cyberpunkred\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Handle a user asking for a tarot card.
 */
class HandleRequest extends FormRequest
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'handle' => [
                'filled',
                'required',
            ],
        ];
    }
}
