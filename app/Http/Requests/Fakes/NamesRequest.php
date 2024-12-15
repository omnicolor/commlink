<?php

declare(strict_types=1);

namespace App\Http\Requests\Fakes;

use Illuminate\Foundation\Http\FormRequest;

class NamesRequest extends FormRequest
{
    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'quantity' => [
                'integer',
                'max:20',
                'min:1',
            ],
        ];
    }
}
