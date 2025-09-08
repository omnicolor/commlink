<?php

declare(strict_types=1);

namespace Modules\Stillfleet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;
use Illuminate\Validation\Validator;
use Override;

class AttributesRequest extends FormRequest
{
    /**
     * @return array<string, string>
     */
    #[Override]
    public function attributes(): array
    {
        return [
            'CHA' => 'charm',
            'COM' => 'combat',
            'MOV' => 'movement',
            'REA' => 'reason',
            'WIL' => 'will',
        ];
    }

    /**
     * @return array<string, array<int, string|In>>
     */
    public function rules(): array
    {
        $allOptions = [
            'option1' => ['d12', 'd10', 'd8', 'd6', 'd6'],
            'option2' => ['d12', 'd10', 'd8', 'd8', 'd4'],
        ];
        $chosenOption = $this->input('dice-option', 'option1');
        $allowedOptions = $allOptions[$chosenOption] ?? [];

        return [
            'dice-option' => [
                'required',
                'in:option1,option2',
            ],
            'COM' => [
                'required',
                Rule::in($allowedOptions),
            ],
            'MOV' => [
                'required',
                Rule::in($allowedOptions),
            ],
            'REA' => [
                'required',
                Rule::in($allowedOptions),
            ],
            'WIL' => [
                'required',
                Rule::in($allowedOptions),
            ],
            'CHA' => [
                'required',
                Rule::in($allowedOptions),
            ],
        ];
    }

    /**
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            /**
             * Validate that the request contains all attribute options.
             */
            function (Validator $validator): void {
                $allOptions = [
                    'option1' => ['d12', 'd10', 'd8', 'd6', 'd6'],
                    'option2' => ['d12', 'd10', 'd8', 'd8', 'd4'],
                ];
                $chosenOption = $this->input('dice-option', 'option1');
                $allowedOptions = $allOptions[$chosenOption];

                $attributes = [
                    $this->input('COM'),
                    $this->input('MOV'),
                    $this->input('REA'),
                    $this->input('WIL'),
                    $this->input('CHA'),
                ];

                sort($allowedOptions);
                sort($attributes);

                if ($allowedOptions !== $attributes) {
                    // No (easy) way to know what field is incorrect, just
                    // flag the first.
                    $validator->errors()->add(
                        'COM',
                        'It looks like you have sent invalid dice choices.',
                    );
                }
            },
        ];
    }
}
