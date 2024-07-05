<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Http\Requests;

use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Modules\Shadowrun5e\Models\MartialArtsStyle;
use Modules\Shadowrun5e\Models\MartialArtsTechnique;
use RuntimeException;

class MartialArtsRequest extends FormRequest
{
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
            'style' => [
                'nullable',
                'alpha_dash',
                function (string $_attribute, $id, Closure $fail): void {
                    try {
                        new MartialArtsStyle($id);
                    } catch (RuntimeException $ex) {
                        $fail($ex->getMessage());
                    }
                },
            ],
            'techniques' => [
                'array',
                'sometimes',
            ],
            'techniques.*' => [
                'alpha_dash',
                'required',
                function (string $attribute, $id, Closure $fail): void {
                    try {
                        new MartialArtsTechnique($id);
                    } catch (RuntimeException $ex) {
                        $fail($ex->getMessage());
                    }
                },
            ],
        ];
    }
}
