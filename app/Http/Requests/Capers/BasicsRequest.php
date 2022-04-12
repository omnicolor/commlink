<?php

declare(strict_types=1);

namespace App\Http\Requests\Capers;

use Closure;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;

class BasicsRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @return array<string, array<int, string|Closure|In|Rule>>
     */
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'background' => [
                    'nullable',
                    'sometimes',
                    'string',
                ],
                'description' => [
                    'nullable',
                    'sometimes',
                    'string',
                ],
                'mannerisms' => [
                    'nullable',
                    'sometimes',
                    'string',
                ],
                'name' => [
                    'required',
                    'string',
                ],
                'type' => [
                    'in:exceptional,caper',
                    'required',
                    'string',
                ],
            ]
        );
    }
}
