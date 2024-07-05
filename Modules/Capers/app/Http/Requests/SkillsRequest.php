<?php

declare(strict_types=1);

namespace Modules\Capers\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;
use Modules\Capers\Models\Skill;

class SkillsRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @return array<string, array<int, string|In|Rule>>
     */
    public function rules(): array
    {
        return [
            'skills' => [
                'array',
                'required',
            ],
            'skills.*' => [
                'string',
                Rule::in(array_keys((array)Skill::all())),
            ],
        ];
    }
}
