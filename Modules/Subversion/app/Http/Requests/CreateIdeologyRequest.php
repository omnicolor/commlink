<?php

declare(strict_types=1);

namespace Modules\Subversion\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;
use Modules\Subversion\Models\Ideology;

use function collect;

class CreateIdeologyRequest extends FormRequest
{
    /**
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
