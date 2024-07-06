<?php

declare(strict_types=1);

namespace Modules\Subversion\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;
use Modules\Subversion\Models\Background;

class CreateBackgroundRequest extends FormRequest
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     * @return array{
     *   background: array<int, In|string>,
     * }
     */
    public function rules(): array
    {
        $backgrounds = collect(Background::all())->pluck('id');
        return [
            'background' => [
                'required',
                Rule::in($backgrounds),
            ],
        ];
    }
}
