<?php

declare(strict_types=1);

namespace App\Http\Controllers\StarTrekAdventures;

use App\Http\Controllers\Controller;
use App\Models\StarTrekAdventures\Character;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * @psalm-suppress UnusedClass
 */
class CharactersController extends Controller
{
    public function list(Request $request): View
    {
        /** @var User */
        $user = $request->user();

        return view(
            'StarTrekAdventures.characters',
            [
                'characters' => $user->characters('star-trek-adventures')->get(),
            ]
        );
    }

    public function view(Request $request, Character $character): View
    {
        $user = $request->user();
        return view(
            'StarTrekAdventures.character',
            ['character' => $character, 'user' => $user]
        );
    }
}
