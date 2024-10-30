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

class CharactersController extends Controller
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function index(Request $request): JsonResource
    {
        /** @var User */
        $user = $request->user();
        return CharacterResource::collection(
            Character::where('owner', $user->email)->get()
        );
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function show(Request $request, Character $character): JsonResource
    {
        /** @var User */
        $user = $request->user();
        $campaign = $character->campaign();
        abort_if(
            $user->email !== $character->owner
            && (null === $campaign || $user->isNot($campaign->gamemaster)),
            Response::HTTP_NOT_FOUND
        );
        return new CharacterResource($character);
    }
}
