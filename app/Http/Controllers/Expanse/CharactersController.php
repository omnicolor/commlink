<?php

declare(strict_types=1);

namespace App\Http\Controllers\Expanse;

use App\Http\Controllers\Controller;
use App\Http\Resources\Expanse\CharacterResource;
use App\Models\Expanse\Character;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\View\View;

/**
 * Controller for interacting with Expanse characters.
 */
class CharactersController extends Controller
{
    /**
     * Return a collection of Expanse characters for the logged in user.
     * @return JsonResource
     */
    public function index(): JsonResource
    {
        return CharacterResource::collection(
            // @phpstan-ignore-next-line
            Character::where('owner', \Auth::user()->email)->get()
        );
    }

    /**
     * View all of the logged in user's characters.
     * @return View
     */
    public function list(): View
    {
        return view('Expanse.characters');
    }

    /**
     * Return a single Expanse character.
     * @param string $identifier
     * @return CharacterResource
     */
    public function show(string $identifier): CharacterResource
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
            'Expanse.character',
            ['character' => $character, 'user' => $user]
        );
    }
}
