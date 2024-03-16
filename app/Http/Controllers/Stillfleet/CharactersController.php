<?php

declare(strict_types=1);

namespace App\Http\Controllers\Stillfleet;

use App\Http\Controllers\Controller;
use App\Http\Requests\Stillfleet\RoleRequest;
use App\Models\Stillfleet\Character;
use App\Models\Stillfleet\PartialCharacter;
use App\Models\Stillfleet\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
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
        $user = $request->user();

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
                $chosenRole = null;
                if (isset($character->roles[0])) {
                    $chosenRole = $character->roles[0];
                }
                return view(
                    'Stillfleet.create-class',
                    [
                        'character' => $character,
                        'chosenRole' => $chosenRole,
                        'creating' => 'class',
                        'roles' => Role::all(),
                        'user' => $user,
                    ],
                );
            case 'class-powers':
                return view(
                    'Stillfleet.create-class-powers',
                );
            default:
                abort(
                    Response::HTTP_NOT_FOUND,
                    'That step of character creation was not found.',
                );
        }
    }

    public function saveClass(RoleRequest $request): RedirectResponse
    {
        /** @var User */
        $user = $request->user();

        $characterId = $request->session()->get('stillfleet-partial');
        $character = PartialCharacter::where('_id', $characterId)
            ->where('owner', $user->email)
            ->firstOrFail();

        if (0 === count($character->roles)) {
            // First time choosing a class.
            $character->roles = [
                [
                    'id' => $request->role,
                    'level' => 1,
                    'powers' => [],
                ],
            ];
            $character->save();
            return new RedirectResponse('/characters/stillfleet/create/class-powers');
        }
        $chosenRole = $character->roles[0];

        if ($chosenRole->id === $request->role) {
            // Updating to the same class.
            return new RedirectResponse('/characters/stillfleet/create/class-powers');
        }

        // Chosing a new class, remove their powers.
        $character->roles = [
            [
                'id' => $request->role,
                'level' => 1,
                'powers' => [],
            ],
        ];
        $character->save();
        return new RedirectResponse('/characters/stillfleet/create/class-powers');
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

    public function view(Request $request, Character $character): View
    {
        $user = $request->user();

        return view(
            'Stillfleet.character',
            ['character' => $character, 'user' => $user]
        );
    }
}
