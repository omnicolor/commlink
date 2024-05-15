<?php

declare(strict_types=1);

namespace App\Http\Controllers\Subversion;

use App\Http\Controllers\Controller;
use App\Models\Subversion\Character;
use App\Models\Subversion\Lineage;
use App\Models\Subversion\PartialCharacter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CharactersController extends Controller
{
    /**
     * @psalm-suppress PossiblyUnusedParam
     */
    public function create(
        Request $request,
        ?string $step = null,
    ): RedirectResponse | View {
        $user = $request->user();
        $character = new PartialCharacter();
        return view(
            'Subversion.create-lineage',
            [
                'character' => $character,
                'lineageId' => $character->lineage?->id,
                'lineageOptionId' => $character->lineage?->option?->id,
                'lineages' => Lineage::all(),
                'user' => $user,
            ],
        );
    }

    public function view(Character $character): View
    {
        $user = Auth::user();
        return view(
            'Subversion.character',
            ['character' => $character, 'user' => $user],
        );
    }
}
