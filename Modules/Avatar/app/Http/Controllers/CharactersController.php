<?php

declare(strict_types=1);

namespace Modules\Avatar\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Modules\Avatar\Models\Character;

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
            'avatar::character',
            ['character' => $character, 'user' => $user]
        );
    }
}
