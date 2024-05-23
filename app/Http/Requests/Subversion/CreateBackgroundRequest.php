<?php

declare(strict_types=1);

namespace App\Http\Requests\Subversion;

use App\Models\Subversion\Background;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;

class CreateBackgroundRequest extends FormRequest
{
    /**
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
