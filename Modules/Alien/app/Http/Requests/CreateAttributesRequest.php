<?php

declare(strict_types=1);

namespace Modules\Alien\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Modules\Alien\Http\Controllers\CharactersController;
use Modules\Alien\Models\PartialCharacter;

class CreateAttributesRequest extends FormRequest
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        /** @var User */
        $user = $this->user();
        $characterId = $this->session()->get(CharactersController::SESSION_KEY);
        $character = PartialCharacter::where('owner', $user->email)
            ->where('_id', $characterId)
            ->firstOrFail();
        $attribute = $character->career?->keyAttribute;

        return [
            'agility' => [
                'integer',
                'agility' === $attribute ? 'max:5' : 'max:4',
                'min:2',
                'required',
            ],
            'empathy' => [
                'integer',
                'empathy' === $attribute ? 'max:5' : 'max:4',
                'min:2',
                'required',
            ],
            'strength' => [
                'integer',
                'strength' === $attribute ? 'max:5' : 'max:4',
                'min:2',
                'required',
            ],
            'wits' => [
                'integer',
                'wits' === $attribute ? 'max:5' : 'max:4',
                'min:2',
                'required',
            ],
        ];
    }
}
