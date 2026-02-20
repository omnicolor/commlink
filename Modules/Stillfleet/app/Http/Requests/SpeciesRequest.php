<?php

declare(strict_types=1);

namespace Modules\Stillfleet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;
use Modules\Stillfleet\Models\Species;

class SpeciesRequest extends FormRequest
{
    /**
     * @return array<string, array<int, In|string>>
     */
    public function rules(): array
    {
        $species = collect(Species::all())->pluck('id');
        return [
            'species' => [
                'required',
                Rule::in($species),
            ],
        ];
    }
}
