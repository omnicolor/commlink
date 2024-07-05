<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Http\Requests;

use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Modules\Shadowrun5e\Models\Quality;
use RuntimeException;

class QualitiesRequest extends FormRequest
{
    /**
     * Get the error messages for the defined validation rules.
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'quality.*.alpha_dash' => 'The quality ID was invalid.',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     * @psalm-suppress PossiblyUnusedMethod
     * @return array<string, array<int, Closure|string>>
     */
    public function rules(): array
    {
        return [
            'nav' => [
                'in:next,prev',
                'required',
            ],
            'quality' => [
                'array',
                'sometimes',
            ],
            'quality.*' => [
                'alpha_dash',
                'required',
                function (string $_attribute, string $id, Closure $fail): void {
                    $id = explode('_', $id);
                    $id = $id[0];

                    try {
                        new Quality($id);
                    } catch (RuntimeException $ex) {
                        $fail($ex->getMessage() . '.');
                    }
                },
            ],
        ];
    }
}
