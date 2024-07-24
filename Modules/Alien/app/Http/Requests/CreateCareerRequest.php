<?php

declare(strict_types=1);

namespace Modules\Alien\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;
use Modules\Alien\Models\Career;

class CreateCareerRequest extends FormRequest
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     * @return array<string, array<int, In|string>>
     */
    public function rules(): array
    {
        $careers = collect(Career::all())
            ->keyBy(function (Career $career): string {
                return $career->id;
            })
            ->keys();
        return [
            'career' => [
                'required',
                Rule::in($careers),
            ],
            'name' => [
                'required',
                'string',
            ],
        ];
    }
}
