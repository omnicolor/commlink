<?php

declare(strict_types=1);

namespace Modules\Blistercritters\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Modules\Blistercritters\Http\Resources\CharacterResource;
use Modules\Blistercritters\Models\Character;

use function view;

class CharactersController extends Controller
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function index(Request $request): JsonResource
    {
        return CharacterResource::collection(
            // @phpstan-ignore-next-line
            Character::where('owner', $request->user()->email)->get()
        );
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function show(Request $request, string $id): JsonResource
    {
        /** @var Character */
        $character = Character::findOrFail($id);
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

    public function view(Request $request, Character $character): View
    {
        $user = $request->user();
        return view(
            'blistercritters::character',
            ['character' => $character, 'user' => $user]
        );
    }
}
