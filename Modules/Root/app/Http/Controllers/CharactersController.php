<?php

declare(strict_types=1);

namespace Modules\Root\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Modules\Root\Http\Resources\CharacterResource;
use Modules\Root\Models\Character;
use Modules\Root\Models\Move;

use function abort_if;
use function view;

class CharactersController extends Controller
{
    public function index(Request $request): JsonResource
    {
        /** @var User */
        $user = $request->user();
        return CharacterResource::collection(
            Character::where('owner', $user->email->address)->get()
        );
    }

    public function show(Request $request, Character $character): JsonResource
    {
        /** @var User */
        $user = $request->user();
        $campaign = $character->campaign();
        abort_if(
            !$user->email->is($character->owner)
            && (null === $campaign || $user->isNot($campaign->gamemaster)),
            Response::HTTP_NOT_FOUND
        );
        return new CharacterResource($character);
    }

    public function view(Request $request, Character $character): View
    {
        $starting_weapon_skills = $character->playbook
            ->starting_weapon_moves;
        // @phpstan-ignore larastan.noUnnecessaryCollectionCall
        $weapon_skills = Move::weapon()
            ->get()
            ->diff($starting_weapon_skills);
        return view(
            'root::character',
            [
                'character' => $character,
                'natures' => $character->playbook->natures,
                'weapon_skills_starting' => $starting_weapon_skills,
                'weapon_skills' => $weapon_skills,
                'user' => $request->user(),
            ]
        );
    }
}
