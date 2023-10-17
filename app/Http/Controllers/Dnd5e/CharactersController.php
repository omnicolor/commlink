<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dnd5e;

use App\Http\Controllers\Controller;
use App\Http\Resources\Dnd5e\CharacterResource;
use App\Models\Dnd5e\Character;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

/**
 * Controller for interacting with D&D 5E characters.
 */
class CharactersController extends Controller
{
    /**
     * Return a collection of characters for the logged in user.
     * @return JsonResource
     */
    public function index(): JsonResource
    {
        return CharacterResource::collection(
            // @phpstan-ignore-next-line
            Character::where('owner', Auth::user()->email)->get()
        );
    }

    /**
     * Return a single D&D 5E character.
     * @param string $identifier
     * @return JsonResource
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
}
