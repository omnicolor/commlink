<?php

declare(strict_types=1);

namespace Modules\Dnd5e\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\View\View;
use Modules\Dnd5e\Http\Resources\CharacterResource;
use Modules\Dnd5e\Models\Character;

use function view;

class CharactersController extends Controller
{
    public function index(Request $request): JsonResource
    {
        return CharacterResource::collection(
            Character::where('owner', $request->user()?->email->address)->get()
        );
    }

    public function show(Request $request, string $identifier): JsonResource
    {
        $email = $request->user()?->email->address;
        return new CharacterResource(
            Character::where('_id', $identifier)
                ->where('owner', $email)
                ->firstOrFail()
        );
    }

    public function view(Request $request, Character $character): View
    {
        return view(
            'dnd5e::character',
            [
                'character' => $character,
                'user' => $request->user(),
            ]
        );
    }
}
