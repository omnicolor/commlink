<?php

declare(strict_types=1);

namespace Modules\Capers\Http\Requests;

use Closure;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;
use Modules\Capers\Models\Identity;
use Modules\Capers\Models\Vice;
use Modules\Capers\Models\Virtue;

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
