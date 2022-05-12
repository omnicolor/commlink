<?php

declare(strict_types=1);

namespace App\Http\Controllers\CyberpunkRed;

use App\Http\Requests\CyberpunkRed\HandleRequest;
use App\Http\Requests\CyberpunkRed\LifepathRequest;
use App\Http\Requests\CyberpunkRed\RoleRequest;
use App\Http\Requests\CyberpunkRed\StatsRequest;
use App\Http\Resources\CyberpunkRed\CharacterResource;
use App\Models\CyberpunkRed\Character;
use App\Models\CyberpunkRed\PartialCharacter;
use App\Models\CyberpunkRed\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\View\View;

/**
 * Controller for interacting with Cyberpunk Red characters.
 */
class CharactersController extends \App\Http\Controllers\Controller
{
    /**
     * Based on what has been entered, determine the next step.
     * @param PartialCharacter $character
     * @return string
     */
    protected function nextStep(PartialCharacter $character): string
    {
        if (null === $character->handle) {
            return 'handle';
        }
        if (null === $character->roles) {
            return 'role';
        }
        if (null === $character->lifepath) {
            return 'lifepath';
        }
        if (null === $character->body) {
            return 'stats';
        }
        return 'review';
    }

    /**
     * Find a partial character if the user has already chosen one.
     * @param Request $request
     * @return ?PartialCharacter
     */
    protected function findPartialCharacter(
        Request $request,
        ?string $step
    ): ?PartialCharacter {
        /** @var User */
        $user = \Auth::user();

        // See if the user has already chosen to continue a character.
        $characterId = $request->session()->get('cyberpunkredpartial');

        if (null !== $characterId) {
            // Return the character they're working on.
            return PartialCharacter::where('owner', $user->email)
                ->findOrFail($characterId)
                ->first();
        }
        if (null !== $step) {
            // Maybe they're chosing to continue a character right now.
            $character = PartialCharacter::where('owner', $user->email)
                ->find($step);
            if (null !== $character) {
                $request->session()->put('cyberpunkredpartial', $character->id);
                return $character;
            }
        }

        return null;
    }

    /**
     * Show the form for creating a new character.
     * @param Request $request
     * @param string $step
     * @return RedirectResponse|View
     */
    public function createForm(
        Request $request,
        ?string $step = null
    ): RedirectResponse | View {
        /** @var User */
        $user = \Auth::user();
        if ('new' === $step) {
            $character = PartialCharacter::create([
                'owner' => $user->email,
            ]);
            $request->session()->put('cyberpunkredpartial', $character->id);
            return view(
                'CyberpunkRed.create-handle',
                [
                    'character' => $character,
                    'creating' => 'handle',
                ]
            );
        }
        if ('save' === $step) {
            $request->session()->forget('cyberpunkredpartial');
            $characters = PartialCharacter::where('owner', $user->email)
                ->where('system', 'cyberpunkred')
                ->get();
            return view(
                'CyberpunkRed.choose-character',
                ['characters' => $characters],
            );
        }

        $character = $this->findPartialCharacter($request, $step);

        if (null === $character) {
            // No current character, see if they already have a character they
            // might want to continue.
            $characters = PartialCharacter::where('owner', $user->email)
                ->where('system', 'cyberpunkred')
                ->get();

            if (0 !== count($characters)) {
                return view(
                    'CyberpunkRed.choose-character',
                    [
                        'characters' => $characters,
                        'user' => $user,
                    ],
                );
            }

            // No in-progress characters, create a new one.
            $character = PartialCharacter::create([
                'owner' => $user->email,
            ]);
            $request->session()->put('cyberpunkredpartial', $character->id);
        }

        if (null === $step || $character->id === $step) {
            $step = $this->nextStep($character);
        }

        switch ($step) {
            case 'handle':
                return view(
                    'CyberpunkRed.create-handle',
                    [
                        'creating' => 'handle',
                        'character' => $character,
                    ]
                );
            case 'lifepath':
                $character->initializeLifepath();
                return view(
                    'CyberpunkRed.create-lifepath',
                    [
                        'affectation' => $character->lifepath['affectation']['chosen'],
                        'background' => $character->lifepath['background']['chosen'],
                        'character' => $character,
                        'clothing' => $character->lifepath['clothing']['chosen'],
                        'creating' => 'lifepath',
                        'environment' => $character->lifepath['environment']['chosen'],
                        'feeling' => $character->lifepath['feeling']['chosen'],
                        'hair' => $character->lifepath['hair']['chosen'],
                        'origin' => $character->lifepath['origin']['chosen'],
                        'personality' => $character->lifepath['personality']['chosen'],
                        'possession' => $character->lifepath['possession']['chosen'],
                        'valueMost' => $character->lifepath['value']['chosen'],
                        'valuePerson' => $character->lifepath['person']['chosen'],
                    ],
                );
            case 'review':
                return view(
                    'CyberpunkRed.character',
                    [
                        'character' => $character,
                        'creating' => 'review',
                    ]
                );
            case 'role':
                $role = null;
                if (
                    0 !== count($character->roles ?? [])
                    && isset($character->roles[0])
                ) {
                    $role = $character->roles[0]['role'];
                }
                return view(
                    'CyberpunkRed.create-role',
                    [
                        'character' => $character,
                        'chosenRole' => $role,
                        'creating' => 'role',
                        'roles' => Role::all(),
                    ]
                );
            case 'role-based-lifepath':
                if (
                    0 === count($character->roles)
                    || !isset($character->roles[0])
                ) {
                    // If the character hasn't picked a role yet, they need to
                    // do that first.
                    return redirect('/characters/cyberpunkred/create/role');
                }
                $role = $character->roles[0]['role'];
                return view(
                    // @phpstan-ignore-next-line
                    sprintf(
                        'CyberpunkRed.create-lifepath-%s',
                        strtolower((string)$role),
                    ),
                    [
                        'character' => $character,
                        'creating' => 'role-based-lifepath',
                    ]
                );
            case 'stats':
                return view(
                    'CyberpunkRed.create-stats',
                    [
                        'character' => $character,
                        'creating' => 'stats',
                    ]
                );
            default:
                abort(
                    Response::HTTP_NOT_FOUND,
                    'That step of character creation was not found.',
                );
        }
    }

    /**
     * Store the character's handle.
     * @param HandleRequest $request
     * @return RedirectResponse
     */
    public function storeHandle(HandleRequest $request): RedirectResponse
    {
        $characterId = $request->session()->get('cyberpunkredpartial');
        /** @var User */
        $user = \Auth::user();
        $character = PartialCharacter::where('_id', $characterId)
            ->where('owner', $user->email)
            ->firstOrFail();
        $character->handle = $request->input('handle');
        $character->update();
        return redirect(sprintf(
            '/characters/cyberpunkred/create/%s',
            $this->nextStep($character)
        ));
    }

    /**
     * Store the character's lifepath.
     * @param LifepathRequest $request
     * @return RedirectResponse
     */
    public function storeLifepath(LifepathRequest $request): RedirectResponse
    {
        $characterId = $request->session()->get('cyberpunkredpartial');
        /** @var User */
        $user = \Auth::user();
        $character = PartialCharacter::where('_id', $characterId)
            ->where('owner', $user->email)
            ->firstOrFail();

        $lifepath = $character->lifepath;
        foreach ($request->only(array_keys($character->lifepath)) as $key => $value) {
            $lifepath[$key]['chosen'] = (int)$value;
        }
        $character->lifepath = $lifepath;
        $character->update();

        return redirect(sprintf(
            '/characters/cyberpunkred/create/%s',
            $this->nextStep($character)
        ));
    }

    /**
     * Store the character's role.
     * @param RoleRequest $request
     * @return RedirectResponse
     */
    public function storeRole(RoleRequest $request): RedirectResponse
    {
        $characterId = $request->session()->get('cyberpunkredpartial');
        /** @var User */
        $user = \Auth::user();
        $character = PartialCharacter::where('_id', $characterId)
            ->where('owner', $user->email)
            ->firstOrFail();
        $character->roles = [
            [
                'role' => $request->input('role'),
                'rank' => 4,
            ],
        ];
        $character->update();
        return redirect(sprintf(
            '/characters/cyberpunkred/create/%s',
            $this->nextStep($character)
        ));
    }

    /**
     * Store the character's base stats.
     * @param StatsRequest $request
     * @return RedirectResponse
     */
    public function storeStats(StatsRequest $request): RedirectResponse
    {
        $characterId = $request->session()->get('cyberpunkredpartial');
        /** @var User */
        $user = \Auth::user();
        $character = PartialCharacter::where('_id', $characterId)
            ->where('owner', $user->email)
            ->firstOrFail();
        $character->fill($request->only([
            'body',
            'cool',
            'dexterity',
            'empathy',
            'intelligence',
            'luck',
            'movement',
            'reflexes',
            'technique',
            'willpower',
        ]));
        $character->update();
        return redirect(sprintf(
            '/characters/cyberpunkred/create/%s',
            $this->nextStep($character)
        ));
    }

    /**
     * Return a collection of characters for the logged in user.
     * @return AnonymousResourceCollection<Character>
     */
    public function index(): AnonymousResourceCollection
    {
        return CharacterResource::collection(
            // @phpstan-ignore-next-line
            Character::where('owner', \Auth::user()->email)->get()
        );
    }

    /**
     * Return a single Cyberpunk Red character.
     * @param string $identifier
     * @return JsonResource
     */
    public function show(string $identifier): JsonResource
    {
        // @phpstan-ignore-next-line
        $email = \Auth::user()->email;
        return new CharacterResource(
            Character::where('_id', $identifier)
                ->where('owner', $email)
                ->firstOrFail()
        );
    }

    /**
     * View a character's sheet.
     * @param Character $character
     * @return View
     */
    public function view(Character $character): View
    {
        $user = \Auth::user();
        return view(
            'CyberpunkRed.character',
            [
                'character' => $character,
                'creating' => false,
                'user' => $user,
            ]
        );
    }
}
