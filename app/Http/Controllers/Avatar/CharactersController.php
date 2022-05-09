<?php

declare(strict_types=1);

namespace App\Http\Controllers\Avatar;

use App\Http\Controllers\Controller;
use App\Models\Avatar\Character;
use Auth;
use Illuminate\View\View;

/**
 * Controller for interacting with Avatar characters.
 */
class CharactersController extends Controller
{
    /**
     * View a character's sheet.
     * @param Character $character
     * @return View
     */
    public function view(Character $character): View
    {
        $user = Auth::user();
        return view(
            'Avatar.character',
            ['character' => $character, 'user' => $user]
        );
    }
}
