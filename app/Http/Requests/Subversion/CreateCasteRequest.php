<?php

declare(strict_types=1);

namespace App\Http\Requests\Subversion;

use App\Models\Subversion\Caste;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;

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
