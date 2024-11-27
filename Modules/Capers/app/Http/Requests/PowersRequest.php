<?php

declare(strict_types=1);

namespace Modules\Capers\Http\Requests;

use Closure;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;
use Modules\Capers\Models\Power;
use Modules\Capers\Models\PowerArray;

use function array_merge;
use function assert;
use function count;

class PowersRequest extends BaseRequest
{
    /**
     * Get the error messages for the defined validation rules.
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'options.required' => 'You must choose how many powers to start with.',
            'powers.required' => 'You must choose at least one power.',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array<string, array<int, string|Closure|In|Rule>>
     */
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'powers' => [
                    'array',
                    'required',
                    function (string $attribute, array $value, Closure $fail): void {
                        $count = count($this->input('powers'));
                        $powers = new PowerArray();
                        foreach ($this->input('powers') as $power) {
                            $powers[] = new Power($power);
                        }
                        switch ($this->input('options')) {
                            case 'one-major':
                                if (1 !== $count) {
                                    $fail('You can only choose a *single* power.');
                                    return;
                                }
                                assert($powers[0] instanceof Power);
                                if (Power::TYPE_MAJOR !== $powers[0]->type) {
                                    $fail('You must choose one *major* power.');
                                    return;
                                }
                                break;
                            case 'one-minor':
                                if (1 !== $count) {
                                    $fail('You can only choose a *single* power.');
                                    return;
                                }
                                assert($powers[0] instanceof Power);
                                if (Power::TYPE_MINOR !== $powers[0]->type) {
                                    $fail('You must choose one *minor* power.');
                                    return;
                                }
                                break;
                            case 'two-minor':
                                if (2 !== $count) {
                                    $fail('You must choose *two* minor powers.');
                                    return;
                                }
                                assert($powers[0] instanceof Power);
                                assert($powers[1] instanceof Power);
                                if (
                                    Power::TYPE_MINOR !== $powers[0]->type
                                    || Power::TYPE_MINOR !== $powers[1]->type
                                ) {
                                    $fail('You can only choose two *minor* powers.');
                                    return;
                                }
                                break;
                        }
                    },
                ],
                'options' => [
                    'in:,one-major,one-minor,two-minor',
                    'required',
                ],
            ]
        );
    }
}
