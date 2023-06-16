<?php

declare(strict_types=1);

namespace App\Http\Controllers\Transformers;

use App\Http\Controllers\Controller;
use App\Models\Transformers\Character;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/** @psalm-suppress UnusedClass */
class CharactersController extends Controller
{
    /**
     * View a character's sheet.
     */
    public function view(Character $character): View
    {
        $user = Auth::user();
        return view(
            'Transformers.character',
            ['character' => $character, 'user' => $user]
        );
    }
}
