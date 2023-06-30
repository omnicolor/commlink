<?php

declare(strict_types=1);

namespace App\Http\Controllers\StarTrekAdventures;

use App\Http\Controllers\Controller;
use App\Models\StarTrekAdventures\Character;
use App\Models\User;
use Illuminate\View\View;

/**
 * @psalm-suppress UnusedClass
 */
class CharactersController extends Controller
{
    /**
     * View all of the logged in user's characters for this system.
     * @return View
     */
    public function list(): View
    {
        /** @var User */
        $user = auth()->user();

        return view(
            'StarTrekAdventures.characters',
            [
                'characters' => $user->characters('star-trek-adventures')->get(),
            ]
        );
    }

    /**
     * View a character's sheet.
     * @param Character $character
     * @return View
     */
    public function view(Character $character): View
    {
        $user = auth()->user();
        return view(
            'StarTrekAdventures.character',
            ['character' => $character, 'user' => $user]
        );
    }
}
