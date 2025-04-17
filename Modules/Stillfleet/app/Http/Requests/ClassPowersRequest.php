<?php

declare(strict_types=1);

namespace Modules\Stillfleet\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;
use Modules\Stillfleet\Models\PartialCharacter;
use Modules\Stillfleet\Models\Role;

class ClassPowersRequest extends FormRequest
{
    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'powers' => 'power(s)',
        ];
    }

    /**
     * @return array<string, string>
     */
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

        // Let the controller handle this invalid request.
        if (!isset($character->roles[0])) {
            return [];
        }

        /** @var Role $role */
        $role = $character->roles[0];
        $powers = collect($role->optional_powers)->pluck('id');
        $choices = $role->optional_choices;

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
