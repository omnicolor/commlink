<?php

declare(strict_types=1);

namespace App\Http\Requests\Capers;

use App\Models\Capers\PartialCharacter;
use Closure;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;

class BoostsRequest extends BaseRequest
{
    /**
     * Get the error messages for the defined validation rules.
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'boosts.required' => 'You must choose boosts for your powers.',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array<string, array<int, string|Closure|In|Rule>>
     */
    public function rules(): array
    {
        /** @var \App\Models\User */
        $user = \Auth::user();
        $characterId = $this->session()->get('capers-partial');
        $character = PartialCharacter::where('owner', $user->email)
            ->where('_id', $characterId)
            ->firstOrFail();

        return array_merge(
            parent::rules(),
            [
                'boosts' => [
                    'array',
                    'required',
                    function (string $attribute, array $rawBoosts, Closure $fail) use ($character): void {
                        $powers = [];
                        foreach ($character->powers as $power) {
                            $powers[$power->id] = [
                                'power' => $power,
                                'boosts' => [],
                            ];
                        }
                        foreach ($rawBoosts as $key => $rawBoost) {
                            [$powerId, $boostId] = explode('+', $rawBoost);
                            if (!isset($powers[$powerId])) {
                                $fail(sprintf(
                                    'Character does not have required power '
                                        . '"%s" for boost ID "%s".',
                                    $powerId,
                                    $boostId
                                ));
                                return;
                            }
                            if (!isset($powers[$powerId]['power']->availableBoosts[$boostId])) {
                                $fail(sprintf(
                                    'Boost ID "%s" is not available for power "%s".',
                                    $boostId,
                                    (string)$powers[$powerId]['power']
                                ));
                                return;
                            }
                            $powers[$powerId]['boosts'][] = $boostId;
                            unset($rawBoosts[$key]);
                        }
                        foreach ($powers as $power) {
                            $requiredBoosts = 2 + $power['power']->rank;
                            if (count($power['boosts']) !== $requiredBoosts) {
                                $fail(sprintf(
                                    'Power "%s" must have %d boosts.',
                                    (string)$power['power'],
                                    $requiredBoosts
                                ));
                                return;
                            }
                        }
                    },
                ],
            ]
        );
    }
}
