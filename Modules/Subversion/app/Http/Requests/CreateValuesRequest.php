<?php

declare(strict_types=1);

namespace Modules\Subversion\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateValuesRequest extends FormRequest
{
    /**
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
