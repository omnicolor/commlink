<?php

declare(strict_types=1);

namespace App\Http\Requests\Shadowrun5e;

use App\Models\Shadowrun5e\MartialArtsStyle;
use App\Models\Shadowrun5e\MartialArtsTechnique;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use RuntimeException;

class MartialArtsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
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
