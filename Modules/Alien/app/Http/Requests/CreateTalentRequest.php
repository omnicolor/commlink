<?php

declare(strict_types=1);

namespace Modules\Alien\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;
use Modules\Alien\Http\Controllers\CharactersController;
use Modules\Alien\Models\PartialCharacter;

class CreateTalentRequest extends FormRequest
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     * @return array<string, array<int, In|string>>
     */
    public function rules(): array
    {
        /** @var User */
        $user = $this->user();
        $characterId = $this->session()->get(CharactersController::SESSION_KEY);
        $character = PartialCharacter::where('owner', $user->email)
            ->where('_id', $characterId)
            ->firstOrFail();
        // @phpstan-ignore property.nonObject
        $talents = collect($character->career->talents)->pluck('id');

        return [
            'talent' => [
                'required',
                'string',
                Rule::in($talents),
            ],
        ];
    }
}
