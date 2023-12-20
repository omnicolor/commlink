<?php

declare(strict_types=1);

namespace App\Http\Controllers\Stillfleet;

use App\Http\Controllers\Controller;
use App\Models\Stillfleet\Character;
use App\Models\Stillfleet\PartialCharacter;
use App\Models\Stillfleet\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CharactersController extends Controller
{
    /**
     * View all of the logged in user's characters.
     */
    public function list(): View
    {
        return view('Stillfleet.characters');
    }

    public function create(
        Request $request,
        ?string $step = null
    ): RedirectResponse | Redirector | View {
        /** @var User */
        $user = Auth::user();

        if ('new' === $step) {
            $character = PartialCharacter::create(['owner' => $user->email]);
            $request->session()->put('stillfleet-partial', $character->id);
            return new RedirectResponse('/characters/stillfleet/create/class');
        }

        $character = $this->findPartialCharacter($request, $user, $step);
        if (null !== $character && $step === $character->id) {
            return new RedirectResponse('/characters/stillfleet/create/class');
        }
        if (null === $character) {
            // No current character, see if they already have a character they
            // might want to continue.
            $characters = PartialCharacter::where('owner', $user->email)->get();

            if (0 !== count($characters)) {
                return view(
                    'Stillfleet.choose-character',
                    [
                        'characters' => $characters,
                        'user' => $user,
                    ],
                );
            }

            // No in-progress characters, create a new one.
            $character = PartialCharacter::create(['owner' => $user->email]);
            $request->session()->put('stillfleet-partial', $character->id);
        }

        if (null === $step || $step === $character->id) {
            $step = 'class';
        }

        switch ($step) {
            case 'class':
                return view(
                    'Stillfleet.create-class',
                    [
                        'character' => $character,
                        'creating' => 'class',
                        'roles' => Role::all(),
                        'user' => $user,
                    ],
                );
            default:
                abort(
                    Response::HTTP_NOT_FOUND,
                    'That step of character creation was not found.',
                );
        }
    }

    protected function findPartialCharacter(
        Request $request,
        User $user,
        ?string $step
    ): ?PartialCharacter {
        // See if the user has already chosen to continue a character.
        $characterId = $request->session()->get('stillfleet-partial');

        if (null !== $characterId) {
            // Return the character they're working on.
            return PartialCharacter::where('owner', $user->email)
                ->where('_id', $characterId)
                ->firstOrFail();
        }

        if (null === $step) {
            return null;
        }

        // Maybe they're chosing to continue a character right now.
        $character = PartialCharacter::where('owner', $user->email)
            ->find($step);
        if (null !== $character) {
            $request->session()->put('stillfleet-partial', $character->id);
        }
        return $character;
    }

    public function view(Character $character): View
    {
        /** @var ?User */
        $user = Auth::user();

        return view(
            'Stillfleet.character',
            ['character' => $character, 'user' => $user]
        );
    }
}
