<?php

declare(strict_types=1);

namespace Modules\Subversion\Http\Requests;

use App\Rules\EachIsAValid;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;
use Modules\Subversion\Models\RelationArchetype;
use Modules\Subversion\Models\RelationAspect;
use Modules\Subversion\Models\RelationLevel;
use Modules\Subversion\Models\Skill;

class CreateRelationsRequest extends FormRequest
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     * @return array<string, array<int, string|EachIsAValid|In>>
     */
    public function rules(): array
    {
        $levels = collect(RelationLevel::all())->pluck('id');
        return [
            'relation_archetype' => [
                'array',
                'required',
            ],
            'relation_archetype.*' => [
                new EachIsAValid(RelationArchetype::class),
            ],
            'relation_aspects' => [
                'array',
                'required',
            ],
            'relation_aspects.*' => [
                'nullable',
                new EachIsAValid(RelationAspect::class),
            ],
            'relation_category' => [
                'array',
                'required',
            ],
            'relation_category.*' => [
                'nullable',
            ],
            'relation_faction' => [
                'array',
                'required',
            ],
            'relation_faction.*' => [
                'nullable',
            ],
            'relation_level' => [
                'array',
                'required',
            ],
            'relation_level.*' => [
                Rule::in($levels),
            ],
            'relation_name' => [
                'array',
                'required',
            ],
            'relation_name.*' => [
                'string',
            ],
            'relation_notes' => [
                'array',
                'required',
            ],
            'relation_notes.*' => [
                'nullable',
                'string',
            ],
            'relation_skill' => [
                'array',
                'required',
            ],
            'relation_skill.*' => [
                new EachIsAValid(Skill::class),
            ],
        ];
    }
}
