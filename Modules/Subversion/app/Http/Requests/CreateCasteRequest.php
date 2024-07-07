<?php

declare(strict_types=1);

namespace Modules\Subversion\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;
use Modules\Subversion\Models\Caste;

class CreateCasteRequest extends FormRequest
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     * @return array{
     *   caste: array<int, In|string>,
     * }
     */
    public function rules(): array
    {
        $castes = collect(Caste::all())->pluck('id');
        return [
            'caste' => [
                'required',
                Rule::in($castes),
            ],
        ];
    }
}
