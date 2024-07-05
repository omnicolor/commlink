<?php

declare(strict_types=1);

namespace App\Http\Requests\Subversion;

use App\Models\Subversion\Ideology;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;

class CreateIdeologyRequest extends FormRequest
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     * @return array{
     *   ideology: array<int, In|string>,
     * }
     */
    public function rules(): array
    {
        $ideologies = collect(Ideology::all())->pluck('id');
        return [
            'ideology' => [
                'required',
                Rule::in($ideologies),
            ],
        ];
    }
}
