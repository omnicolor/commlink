<?php

declare(strict_types=1);

namespace App\Http\Requests\Subversion;

use App\Models\Subversion\Origin;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;

class CreateOriginRequest extends FormRequest
{
    /**
     * @return array{
     *   origin: array<int, In|string>,
     * }
     */
    public function rules(): array
    {
        $origins = collect(Origin::all())->pluck('id');
        return [
            'origin' => [
                'required',
                Rule::in($origins),
            ],
        ];
    }
}
