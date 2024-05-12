<?php

declare(strict_types=1);

namespace App\Http\Controllers\Subversion;

use App\Http\Controllers\Controller;
use App\Models\Subversion\Character;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CharactersController extends Controller
{
    public function view(Character $character): View
    {
        $user = Auth::user();
        return view(
            'Subversion.character',
            ['character' => $character, 'user' => $user],
        );
    }
}
