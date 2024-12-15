<?php

declare(strict_types=1);

namespace Modules\Alien\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;
use Modules\Alien\Models\Skill;

class RollRequest extends FormRequest
{
    /**
     * @return array{
     *   character: array<int, string>,
     *   skill: array<int, In|string>,
     *   type: array<int, string>
     * }
     */
    public function rules(): array
    {
        $skills = collect(Skill::all())->pluck('id');
        return [
            'character' => [
                'required',
                'string',
            ],
            'skill' => [
                'required',
                Rule::in($skills),
            ],
            'type' => [
                'in:skill',
                'required',
            ],
        ];
    }
}
