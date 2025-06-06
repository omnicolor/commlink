<?php

declare(strict_types=1);

namespace Modules\Capers\Http\Requests;

use Closure;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;
use Override;

use function array_merge;

class TraitsRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @return array<string, array<int, string|Closure|In|Rule>>
     */
    #[Override]
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'trait-high' => [
                    'in:agility,charisma,expertise,perception,resilience,strength',
                    'required',
                    'string',
                ],
                'trait-low' => [
                    'in:agility,charisma,expertise,perception,resilience,strength',
                    'required',
                    'string',
                ],
            ]
        );
    }
}
