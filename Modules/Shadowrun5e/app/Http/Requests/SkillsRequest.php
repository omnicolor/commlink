<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Http\Requests;

use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Modules\Shadowrun5e\Models\ActiveSkill;
use Modules\Shadowrun5e\Models\SkillGroup;
use RuntimeException;

class SkillsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @psalm-suppress PossiblyUnusedMethod
     * @return array<string, array<int, Closure|string>>
     */
    public function rules(): array
    {
        return [
            'group-levels' => [
                'array',
                'sometimes',
            ],
            'group-levels.*' => [
                'integer',
                'required',
            ],
            'group-names' => [
                'array',
                'sometimes',
            ],
            'group-names.*' => [
                'alpha_dash',
                'required',
                function (string $_attribute, string $id, Closure $fail): void {
                    try {
                        new SkillGroup($id, 1);
                    } catch (RuntimeException $ex) {
                        $fail($ex->getMessage());
                    }
                },
            ],
            'nav' => [
                'in:next,prev',
                'required',
            ],
            'skill-levels' => [
                'array',
                'sometimes',
            ],
            'skill-levels.*' => [
                'integer',
                'required',
            ],
            'skill-names' => [
                'array',
                'sometimes',
            ],
            'skill-names.*' => [
                'alpha_dash',
                'required',
                function (string $_attribute, string $id, Closure $fail): void {
                    try {
                        new ActiveSkill($id, 1);
                    } catch (RuntimeException $ex) {
                        $fail($ex->getMessage());
                    }
                },
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
