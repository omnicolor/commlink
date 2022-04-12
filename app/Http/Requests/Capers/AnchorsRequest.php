<?php

declare(strict_types=1);

namespace App\Http\Requests\Capers;

use App\Models\Capers\Identity;
use App\Models\Capers\Vice;
use App\Models\Capers\Virtue;
use Closure;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;

class AnchorsRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @return array<string, array<int, string|Closure|In|Rule>>
     */
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'identity' => [
                    'required',
                    'string',
                    Rule::in(array_keys(Identity::all())),
                ],
                'vice' => [
                    'required',
                    'string',
                    Rule::in(array_keys(Vice::all())),
                ],
                'virtue' => [
                    'required',
                    'string',
                    Rule::in(array_keys(Virtue::all())),
                ],
            ]
        );
    }
}
