<?php

declare(strict_types=1);

namespace Modules\Alien\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;
use Modules\Alien\Http\Controllers\CharactersController;
use Modules\Alien\Models\Career;
use Modules\Alien\Models\PartialCharacter;
use Modules\Alien\Rules\OneFromRow;

class CreateGearRequest extends FormRequest
{
    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'gear.0' => 'An item you selected is not valid for your career.',
            'gear.1' => 'An item you selected is not valid for your career.',
            'gear.size' => 'You must choose only two items.',
            'gear.required' => 'You must choose two items.',
        ];
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     * @return array<string, array<int, In|OneFromRow|string>>
     */
    public function rules(): array
    {
        /** @var User */
        $user = $this->user();
        $characterId = $this->session()->get(CharactersController::SESSION_KEY);
        $character = PartialCharacter::where('owner', $user->email)
            ->where('_id', $characterId)
            ->firstOrFail();
        /** @var Career */
        $career = $character->career;
        $gear = collect($career->gear)->flatten(1)->pluck('id');

        return [
            'gear' => [
                'array',
                'required',
                'size:2',
                new OneFromRow($career),
            ],
            'gear.*' => [
                'string',
                Rule::in($gear),
            ],
        ];
    }
}
