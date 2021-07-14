<?php

declare(strict_types=1);

namespace App\Http\Controllers\CyberpunkRed;

use App\Http\Resources\CyberpunkRed\CharacterResource;
use App\Models\CyberpunkRed\Character;
use App\Models\User;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\View\View;

/**
 * Controller for interacting with Cyberpunk Red characters.
 */
class CharactersController extends \App\Http\Controllers\Controller
{
    /**
     * Return a collection of characters for the logged in user.
     * @return AnonymousResourceCollection<Character>
     */
    public function index(): AnonymousResourceCollection
    {
        return CharacterResource::collection(
            // @phpstan-ignore-next-line
            Character::where('owner', \Auth::user()->email)->get()
        );
    }

    /**
     * Return a single Cyberpunk Red character.
     * @param string $identifier
     * @return JsonResource
     */
    public function show(string $identifier): JsonResource
    {
        // @phpstan-ignore-next-line
        $email = \Auth::user()->email;
        return new CharacterResource(
            Character::where('_id', $identifier)
                ->where('owner', $email)
                ->firstOrFail()
        );
    }

    /**
     * View a character's sheet.
     * @param Character $character
     * @return View
     */
    public function view(Character $character): View
    {
        $user = \Auth::user();
        return view(
            'cyberpunkred.character',
            ['character' => $character, 'user' => $user]
        );
    }
}
