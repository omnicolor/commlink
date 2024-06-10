<?php

declare(strict_types=1);

namespace App\Http\Controllers\Avatar;

use App\Http\Controllers\Controller;
use App\Models\Avatar\Character;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

use function view;

/**
 * Controller for interacting with Avatar characters.
 */
class CharactersController extends Controller
{
    public function view(Character $character): View
    {
        $user = Auth::user();
        return view(
            'Avatar.character',
            ['character' => $character, 'user' => $user]
        );
    }
}
