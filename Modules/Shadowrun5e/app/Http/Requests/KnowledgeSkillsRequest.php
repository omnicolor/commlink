<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KnowledgeSkillsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @psalm-suppress PossiblyUnusedMethod
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'nav' => [
                'in:next,prev',
                'required',
            ],
            'skill-levels' => [
                'array',
                'sometimes',
            ],
            'skill-levels.*' => [
                'required',
            ],
            'skill-names' => [
                'array',
                'sometimes',
            ],
            'skill-names.*' => [
                'string',
                'required',
            ],
            'skill-specializations' => [
                'array',
                'sometimes',
            ],
            'skill-specializations.*' => [
                'nullable',
                'string',
            ],
        ];
    }
}
