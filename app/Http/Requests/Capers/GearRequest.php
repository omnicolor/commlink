<?php

declare(strict_types=1);

namespace App\Http\Requests\Capers;

use App\Models\Capers\Gear;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;

class GearRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @return array<string, array<int, string|In|Rule>>
     */
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'gear' => [
                    'array',
                    'required_with:quantity',
                    'sometimes',
                ],
                'gear.*' => [
                    'required',
                    'string',
                    Rule::in(array_keys((array)Gear::all())),
                ],
                'quantity' => [
                    'array',
                    'required_with:gear',
                    'sometimes',
                ],
                'quantity.*' => [
                    'integer',
                    'min:0',
                    'required',
                ],
            ]
        );
    }
}
