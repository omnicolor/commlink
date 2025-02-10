<?php

declare(strict_types=1);

namespace Modules\Alien\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Modules\Alien\Http\Controllers\CharactersController;
use Modules\Alien\Models\PartialCharacter;
use Modules\Alien\Models\Skill;

use function in_array;

class CreateSkillsRequest extends FormRequest
{
    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        /** @var User */
        $user = $this->user();
        $characterId = $this->session()->get(CharactersController::SESSION_KEY);
        /** @var PartialCharacter */
        $character = PartialCharacter::where('owner', $user->email)
            ->where('_id', $characterId)
            ->firstOrFail();
        $keySkills = collect($character->career?->keySkills)
            ->pluck('id')
            ->toArray();
        $skills = collect(Skill::all())->pluck('id');

        $rules = [];
        foreach ($skills as $skill) {
            $rules[$skill] = [
                'integer',
                in_array($skill, $keySkills, true) ? 'max:3' : 'max:1',
                'min:0',
                'required',
            ];
        }
        return $rules;
    }
}
