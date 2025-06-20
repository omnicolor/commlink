<?php

declare(strict_types=1);

namespace Modules\Alien\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;
use Modules\Alien\Http\Controllers\CharactersController;
use Modules\Alien\Models\PartialCharacter;

use function collect;

class CreateTalentRequest extends FormRequest
{
    /**
     * @return array<string, array<int, In|string>>
     */
    public function rules(): array
    {
        /** @var User */
        $user = $this->user();
        $characterId = $this->session()->get(CharactersController::SESSION_KEY);
        /** @var PartialCharacter */
        $character = PartialCharacter::where('owner', $user->email->address)
            ->where('_id', $characterId)
            ->firstOrFail();
        $talents = collect($character->career?->talents)->pluck('id');

        return [
            'talent' => [
                'required',
                'string',
                Rule::in($talents),
            ],
        ];
    }
}
