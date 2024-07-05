<?php

declare(strict_types=1);

namespace App\Http\Requests\Subversion;

use Illuminate\Foundation\Http\FormRequest;

class CreateValuesRequest extends FormRequest
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     * @return array{
     *   corrupted: array<int, string>,
     *   value1: array<int, string>,
     *   value2: array<int, string>,
     *   value3: array<int, string>,
     * }
     */
    public function rules(): array
    {
        return [
            'corrupted' => [
                'boolean',
                'sometimes',
            ],
            'value1' => [
                'required',
                'string',
            ],
            'value2' => [
                'required',
                'string',
            ],
            'value3' => [
                'required',
                'string',
            ],
        ];
    }
}
