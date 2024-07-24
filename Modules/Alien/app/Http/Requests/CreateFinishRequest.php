<?php

declare(strict_types=1);

namespace Modules\Alien\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateFinishRequest extends FormRequest
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'agenda' => [
                'nullable',
                'string',
            ],
            'appearance' => [
                'nullable',
                'string',
            ],
            'buddy' => [
                'nullable',
                'string',
            ],
            'rival' => [
                'nullable',
                'string',
            ],
        ];
    }
}
