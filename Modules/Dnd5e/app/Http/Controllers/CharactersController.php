<?php

declare(strict_types=1);

namespace Modules\Dnd5e\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Modules\Dnd5e\Http\Resources\CharacterResource;
use Modules\Dnd5e\Models\Character;

use function view;

class CharactersController extends Controller
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function index(): JsonResource
    {
        return CharacterResource::collection(
            // @phpstan-ignore-next-line
            Character::where('owner', Auth::user()->email)->get()
        );
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function show(string $identifier): JsonResource
    {
        // @phpstan-ignore-next-line
        $email = Auth::user()->email;
        return new CharacterResource(
            Character::where('_id', $identifier)
                ->where('owner', $email)
                ->firstOrFail()
        );
    }

    public function view(Character $character): View
    {
        $user = Auth::user();
        return view(
            'dnd5e::character',
            ['character' => $character, 'user' => $user]
        );
    }
}
