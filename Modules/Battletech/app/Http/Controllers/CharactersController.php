<?php

declare(strict_types=1);

namespace Modules\Battletech\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Modules\Battletech\Models\Character;
use Modules\Battletech\Transformers\CharacterTransformer;

use function view;

class CharactersController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return CharacterTransformer::collection(
            Character::where('owner', $request->user()?->email->address)->get()
        );
    }

    public function show(Request $request, Character $character): CharacterTransformer
    {
        /** @var User */
        $user = $request->user();
        $campaign = $character->campaign();
        abort_if(
            !$user->email->is($character->owner)
            && (null === $campaign || $user->isNot($campaign->gamemaster)),
            Response::HTTP_NOT_FOUND
        );
        return new CharacterTransformer($character);
    }

    public function view(Request $request, Character $character): View
    {
        $user = $request->user();
        return view(
            'battletech::character',
            ['character' => $character, 'user' => $user]
        );
    }
}
