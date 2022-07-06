<?php

declare(strict_types=1);

namespace App\Http\Requests\Cyberpunkred;

use Illuminate\Foundation\Http\FormRequest;

class HandleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
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
