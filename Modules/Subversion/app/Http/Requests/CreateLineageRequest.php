<?php

declare(strict_types=1);

namespace Modules\Subversion\Http\Requests;

use Closure;
use Exception;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;
use Modules\Subversion\Models\Lineage;

use function collect;

class CreateLineageRequest extends FormRequest
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     * @return array{
     *   lineage: array<int, In|string>,
     *   name: array<int, string>,
     *   option: array<int, Closure|string>,
     * }
     */
    public function rules(): array
    {
        $lineages = collect(Lineage::all())->pluck('id');
        $lineage = $this->input('lineage');
        return [
            'lineage' => [
                'required',
                Rule::in($lineages),
            ],
            'name' => [
                'required',
                'string',
            ],
            'option' => [
                'required',
                'string',
                function (string $field, string $option, Closure $fail) use ($lineage): void {
                    try {
                        $lineage = new Lineage($lineage);
                    } catch (Exception) {
                        $fail('Lineage option can\'t be verified on unknown lineage.');
                        return;
                    }
                    if (!collect($lineage->options)->keys()->contains($option)) {
                        $fail('Lineage option is not valid for lineage.');
                    }
                },
            ],
        ];
    }
}
