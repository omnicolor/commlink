<?php

declare(strict_types=1);

namespace App\Http\Requests\Transformers;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @psalm-suppress UnusedClass
 */
class AltModeRequest extends FormRequest
{
    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'mode' => [
                'required',
                'string',
            ],
        ];
    }
}
