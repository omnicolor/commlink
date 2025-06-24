<?php

declare(strict_types=1);

namespace Modules\Stillfleet\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;
use Modules\Stillfleet\Models\PartialCharacter;
use Modules\Stillfleet\Models\Species;
use Override;

class SpeciesPowersRequest extends FormRequest
{
    /**
     * @return array<string, string>
     */
    #[Override]
    public function attributes(): array
    {
        return [
            'powers' => 'power(s)',
        ];
    }

    /**
     * @return array<string, string>
     */
    #[Override]
    public function messages(): array
    {
        return [
            'powers.max' => 'You may not add more than :max :attribute.',
        ];
    }

    /**
     * @return array<string, array<int, string|In>>
     */
    public function rules(): array
    {
        /** @var User */
        $user = $this->user();

        $characterId = $this->session()->get('stillfleet-partial');
        /** @var PartialCharacter */
        $character = PartialCharacter::where('_id', $characterId)
            ->where('owner', $user->email)
            ->firstOrFail();

        if (null === $character->species) {
            return [];
        }

        /** @var Species */
        $species = $character->species;
        $powers = collect($species->optional_powers)->pluck('id');
        $choices = $species->powers_choose;

        return [
            'powers' => [
                'array',
                'max:' . $choices,
                'required',
                Rule::in($powers),
            ],
        ];
    }
}
