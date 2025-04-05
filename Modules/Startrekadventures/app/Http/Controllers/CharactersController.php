<?php

declare(strict_types=1);

namespace Modules\Startrekadventures\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\View\View;
use Modules\Startrekadventures\Http\Resources\CharacterResource;
use Modules\Startrekadventures\Models\Character;

use function view;

class CharactersController extends Controller
{
    public function list(Request $request): View
    {
        /** @var User */
        $user = $request->user();

        return view(
            'startrekadventures::characters',
            ['characters' => $user->characters('startrekadventures')->get()],
        );
    }

    public function index(Request $request): JsonResource
    {
        $user = $request->user();
        return CharacterResource::collection(
            Character::where('owner', $user?->email)->get()
        )
            ->additional(['links' => ['self' => route('startrekadventures.characters.index')]]);
    }

    public function show(Request $request, string $identifier): JsonResource
    {
        /** @var User */
        $user = $request->user();
        return new CharacterResource(
            Character::where('_id', $identifier)
                ->where('owner', $user->email)
                ->firstOrFail()
        );
    }

    public function view(Request $request, Character $character): View
    {
        $user = $request->user();
        return view(
            'startrekadventures::character',
            ['character' => $character, 'user' => $user]
        );
    }
}
