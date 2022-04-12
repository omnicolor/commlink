<?php

declare(strict_types=1);

namespace App\Http\Requests\Shadowrun5e;

use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;
use Illuminate\Validation\Rules\RequiredIf;

class StandardPriorityRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @return array<string, array<int, string|Closure|In|RequiredIf>>
     */
    public function rules(Request $request): array
    {
        $validMetatypes = [
            'dwarf',
            'elf',
            'human',
            'ork',
            'troll',
        ];
        $validPriorityValues = [
            'attributes',
            'magic',
            'metatype',
            'resources',
            'skills',
        ];
        $validMagic = [
            'a' => [
                'magician',
                'mystic',
                'technomancer',
            ],
            'b' => [
                'adept',
                'aspected',
                'magician',
                'mystic',
                'technomancer',
            ],
            'c' => [
                'adept',
                'aspected',
                'magician',
                'mystic',
                'technomancer',
            ],
            'd' => [
                'adept',
                'aspected',
            ],
            'e' => [],
        ];
        return [
            'magic' => [
                Rule::requiredIf(function () use ($request): bool {
                    return 'magic' !== $request->input('priority-e');
                }),
                function (string $attribute, string $value, Closure $fail) use ($request, $validMagic): void {
                    for ($i = ord('a'); $i <= ord('e'); $i++) {
                        if (
                            'magic' === $request->input('priority-' . chr($i))
                            && !in_array($value, $validMagic[chr($i)], true)
                        ) {
                            $fail(sprintf(
                                '%s is not a valid magic selection for priority %s.',
                                ucfirst($value),
                                strtoupper(chr($i)),
                            ));
                        }
                    }
                },
            ],
            'metatype' => [
                'required',
                Rule::in($validMetatypes),
            ],
            'nav' => [
                'in:next,prev',
                'required',
            ],
            'priority-a' => [
                'required',
                Rule::in($validPriorityValues),
            ],
            'priority-b' => [
                'required',
                Rule::in($validPriorityValues),
            ],
            'priority-c' => [
                'required',
                Rule::in($validPriorityValues),
            ],
            'priority-d' => [
                'required',
                Rule::in($validPriorityValues),
            ],
            'priority-e' => [
                'required',
                Rule::in($validPriorityValues),
            ],
        ];
    }
}
