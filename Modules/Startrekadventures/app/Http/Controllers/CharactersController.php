<?php

declare(strict_types=1);

namespace Modules\Startrekadventures\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;
use Modules\Startrekadventures\Models\Character;

use function view;

/**
 * @psalm-suppress UnusedClass
 */
class CharactersController extends Controller
{
    /**
     * @psalm-suppress PossiblyUnusedReturnValue
     */
    public function list(): View
    {
        /** @var User */
        $user = auth()->user();

        return view(
            'startrekadventures::characters',
            [
                'characters' => $user->characters('startrekadventures')->get(),
            ]
        );
    }

    public function view(Character $character): View
    {
        $user = auth()->user();
        return view(
            'startrekadventures::character',
            ['character' => $character, 'user' => $user]
        );
    }
}
