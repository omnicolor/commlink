<?php

declare(strict_types=1);

namespace Modules\Stillfleet\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\View\View;
use Modules\Stillfleet\Http\Requests\RoleRequest;
use Modules\Stillfleet\Http\Resources\CharacterResource;
use Modules\Stillfleet\Models\Character;
use Modules\Stillfleet\Models\PartialCharacter;
use Modules\Stillfleet\Models\Role;

use function abort;
use function abort_if;
use function assert;
use function count;
use function route;
use function view;

class CharactersController extends Controller
{
    /**
     * View all of the logged in user's characters.
     */
    public function list(): View
    {
        return view('stillfleet::characters');
    }

    public function create(
        Request $request,
        ?string $step = null
    ): RedirectResponse | Redirector | View {
        /** @var User */
        $user = $request->user();

        if ('new' === $step) {
            $character = PartialCharacter::create([
                'owner' => $user->email->address,
            ]);
            $request->session()->put('stillfleet-partial', $character->id);
            return new RedirectResponse(route('stillfleet.create', 'class'));
        }

        $character = $this->findPartialCharacter($request, $user, $step);
        if ($character instanceof PartialCharacter && $step === $character->id) {
            return new RedirectResponse(route('stillfleet.create', 'class'));
        }
        if (!$character instanceof PartialCharacter) {
            // No current character, see if they already have a character they
            // might want to continue.
            $characters = PartialCharacter::where('owner', $user->email->address)
                ->get();

            if (0 !== count($characters)) {
                return view(
                    'stillfleet::choose-character',
                    [
                        'characters' => $characters,
                        'user' => $user,
                    ],
                );
            }

            // No in-progress characters, create a new one.
            $character = PartialCharacter::create([
                'owner' => $user->email->address,
            ]);
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
                    'stillfleet::create-class',
                    [
                        'character' => $character,
                        'chosenRole' => $chosenRole,
                        'creating' => 'class',
                        'roles' => Role::all(),
                        'user' => $user,
                    ],
                );
            case 'class-powers':
                return view('stillfleet::create-class-powers');
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
        /** @var PartialCharacter */
        $character = PartialCharacter::where('_id', $characterId)
            ->where('owner', $user->email->address)
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
            return new RedirectResponse(route('stillfleet.create', 'class-powers'));
        }
        $chosenRole = $character->roles[0];
        assert($chosenRole instanceof Role);

        if ($chosenRole->id === $request->role) {
            // Updating to the same class.
            return new RedirectResponse(route('stillfleet.create', 'class-powers'));
        }

        // Choosing a new class, remove their powers.
        $character->roles = [
            [
                'id' => $request->role,
                'level' => 1,
                'powers' => [],
            ],
        ];
        $character->save();
        return new RedirectResponse(route('stillfleet.create', 'class-powers'));
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
            /** @var PartialCharacter */
            return PartialCharacter::where('owner', $user->email->address)
                ->where('_id', $characterId)
                ->firstOrFail();
        }

        if (null === $step) {
            return null;
        }

        // Maybe they're choosing to continue a character right now.
        /** @var ?PartialCharacter */
        $character = PartialCharacter::where('owner', $user->email->address)
            ->find($step);
        if (null !== $character) {
            $request->session()->put('stillfleet-partial', $character->id);
        }
        return $character;
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        /** @var User */
        $user = $request->user();

        return CharacterResource::collection(
            Character::where('owner', $user->email->address)->get()
        )
            ->additional(['links' => ['self' => route('stillfleet.characters.index')]]);
    }

    public function show(Request $request, Character $character): CharacterResource
    {
        /** @var User */
        $user = $request->user();

        $campaign = $character->campaign();
        abort_if(
            !$user->email->is($character->owner)
            && (null === $campaign || $user->isNot($campaign->gamemaster)),
            Response::HTTP_NOT_FOUND
        );
        return new CharacterResource($character);
    }

    public function view(Request $request, Character $character): View
    {
        $user = $request->user();

        return view(
            'stillfleet::character',
            ['character' => $character, 'user' => $user],
        );
    }
}
