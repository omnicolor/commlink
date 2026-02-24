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
use Modules\Stillfleet\Enums\VoidwareType;
use Modules\Stillfleet\Http\Requests\AttributesRequest;
use Modules\Stillfleet\Http\Requests\ClassPowersRequest;
use Modules\Stillfleet\Http\Requests\ClassRequest;
use Modules\Stillfleet\Http\Requests\DetailsRequest;
use Modules\Stillfleet\Http\Requests\SpeciesPowersRequest;
use Modules\Stillfleet\Http\Requests\SpeciesRequest;
use Modules\Stillfleet\Http\Resources\CharacterResource;
use Modules\Stillfleet\Models\Character;
use Modules\Stillfleet\Models\CharacterDetails;
use Modules\Stillfleet\Models\Gear;
use Modules\Stillfleet\Models\PartialCharacter;
use Modules\Stillfleet\Models\Role;
use Modules\Stillfleet\Models\Species;
use RuntimeException;

use function abort;
use function abort_if;
use function assert;
use function collect;
use function count;
use function in_array;
use function route;
use function view;

class CharactersController extends Controller
{
    public function list(): View
    {
        return view('stillfleet::characters');
    }

    public function create(
        Request $request,
        ?string $step = null
    ): RedirectResponse | Redirector | View {
        /** @var User $user */
        $user = $request->user();

        if ('new' === $step) {
            $character = PartialCharacter::create([
                'owner' => $user->email->address,
            ]);
            $request->session()->put('stillfleet-partial', $character->id);
            return new RedirectResponse(route('stillfleet.create', 'details'));
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
            $step = 'details';
        }

        switch ($step) {
            case 'attributes':
                if (!isset($character->roles[0])) {
                    return redirect()->route('stillfleet.create', 'class')
                        ->withErrors(['You must choose a class before attributes.']);
                }

                $grit = $character->roles[0]->grit;
                foreach ($grit as &$attribute) {
                    $attribute = strtoupper(substr($attribute, 0, 3));
                }
                return view(
                    'stillfleet::create-attributes',
                    [
                        'character' => $character,
                        'creating' => 'attributes',
                        'option' => $request->old('dice-option') ?? $character->attribute_dice_option ?? null,
                        'grit' => $grit,
                    ],
                );
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
                if (!isset($character->roles[0])) {
                    return redirect()->route('stillfleet.create', 'class')
                        ->withErrors(['You must choose a class before powers.']);
                }
                $role = $character->roles[0];

                $choices = $role->optional_choices;
                $choices2 = null;
                $list = collect($role->optional_powers)->keyBy('id');
                $list2 = [];
                // @codeCoverageIgnoreStart
                if (in_array($role->id, ['jackal', 'mouse'], true)) {
                    $choices = 2;
                    $choices2 = 1;

                    $list2 = $list->except(['rat-out', 'steal', 'vanish']);
                    $list = $list->only(['rat-out', 'steal', 'vanish']);
                } elseif ('pir' === $role->id) {
                    $choices = 2;
                    $choices2 = 1;

                    $list2 = $list->except(['aetherspeak', 'augur', 'listen', 'regenerate']);
                    $list = $list->only(['aetherspeak', 'augur', 'listen', 'regenerate']);
                }
                // @codeCoverageIgnoreEnd

                return view(
                    'stillfleet::create-class-powers',
                    [
                        'character' => $character,
                        'choices' => $choices,
                        'choices2' => $choices2,
                        'creating' => 'class-powers',
                        'list' => $list,
                        'list2' => $list2,
                        'role' => $role,
                        'chosen_powers' => collect($role->added_powers)->pluck('id')->toArray(),
                    ],
                );
            case 'details':
                $details = $character->details;
                return view(
                    'stillfleet::create-details',
                    [
                        'appearance' => $request->old('appearance') ?? $details->appearance,
                        'character' => $character,
                        'company' => $request->old('appearance') ?? $details->company,
                        'creating' => 'details',
                        'crew_nickname' => $request->old('crew_nickname') ?? $details->crew_nickname,
                        'family' => $request->old('family') ?? $details->family,
                        'motivation' => $request->old('motivation') ?? $details->motivation,
                        'origin' => $request->old('origin') ?? $details->origin,
                        'others' => $request->old('others') ?? $details->others,
                        'name' => $request->old('name') ?? $character->name ?? null,
                        'refactor' => $request->old('refactor') ?? $details->refactor,
                        'team' => $request->old('team') ?? $details->team,
                    ]
                );
            case 'gear':
                try {
                    $money = $character->startingMoney();
                } catch (RuntimeException) {
                    return redirect()
                        ->route('stillfleet.create', 'attributes')
                        ->withErrors(['You must set attributes before buying gear.']);
                }
                return view(
                    'stillfleet::create-gear',
                    [
                        'character' => $character,
                        'creating' => 'gear',
                        'gear' => Gear::all(),
                        'money' => $money,
                        'types' => collect(VoidwareType::cases())
                            ->pluck('name', 'value'),
                        'user' => $user,
                    ],
                );
            case 'species':
                return view(
                    'stillfleet::create-species',
                    [
                        'all_species' => Species::all(),
                        'character' => $character,
                        'chosen_species' => $character->species,
                        'creating' => 'species',
                    ],
                );
            case 'species-powers':
                if (null === $character->species) {
                    return redirect()->route('stillfleet.create', 'species')
                        ->withErrors(['You must choose a species before powers.']);
                }
                if (0 === $character->species->powers_choose) {
                    return redirect()->route('stillfleet.create', 'attributes');
                }
                $chosen_powers = collect($character->species->added_powers)
                    ->pluck('id')
                    ->toArray();
                return view(
                    'stillfleet::create-species-powers',
                    [
                        'character' => $character,
                        'chosen_powers' => $chosen_powers,
                        'creating' => 'species-powers',
                    ],
                );
            default:
                abort(
                    Response::HTTP_NOT_FOUND,
                    'That step of character creation was not found.',
                );
        }
    }

    public function saveAttributes(AttributesRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $characterId = $request->session()->get('stillfleet-partial');
        /** @var PartialCharacter $character */
        $character = PartialCharacter::where('_id', $characterId)
            ->where('owner', $user->email->address)
            ->firstOrFail();

        $character->attribute_dice_option = $request->input('dice-option');
        $character->charm = $request->input('CHA');
        $character->combat = $request->input('COM');
        $character->movement = $request->input('MOV');
        $character->reason = $request->input('REA');
        $character->will = $request->input('WIL');
        $character->save();

        return new RedirectResponse(route('stillfleet.create', 'gear'));
    }

    public function saveClass(ClassRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $characterId = $request->session()->get('stillfleet-partial');
        /** @var PartialCharacter $character */
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
            return new RedirectResponse(
                route('stillfleet.create', 'class-powers'),
            );
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

    public function saveClassPowers(ClassPowersRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $characterId = $request->session()->get('stillfleet-partial');
        /** @var PartialCharacter $character */
        $character = PartialCharacter::where('_id', $characterId)
            ->where('owner', $user->email->address)
            ->firstOrFail();

        if (1 !== count($character->roles)) {
            return redirect()
                ->route('stillfleet.create', 'class')
                ->withErrors([
                    'class' => 'You must choose a class before powers.',
                ]);
        }

        $role = $character->roles[0];
        $raw_role = [
            'id' => $role->id,
            'level' => $role->level,
            'powers' => $request->powers,
        ];
        $character->roles = [$raw_role];
        $character->save();

        return new RedirectResponse(route('stillfleet.create', 'species'));
    }

    public function saveDetails(DetailsRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $characterId = $request->session()->get('stillfleet-partial');
        /** @var PartialCharacter $character */
        $character = PartialCharacter::where('_id', $characterId)
            ->where('owner', $user->email->address)
            ->firstOrFail();

        $character->name = $request->name;
        $character->details = CharacterDetails::make($request->input());
        $character->save();

        return new RedirectResponse(route('stillfleet.create', 'class'));
    }

    public function saveSpecies(SpeciesRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $characterId = $request->session()->get('stillfleet-partial');
        /** @var PartialCharacter $character */
        $character = PartialCharacter::where('_id', $characterId)
            ->where('owner', $user->email->address)
            ->firstOrFail();

        if ($character->species?->id !== $request->species) {
            // Choosing a new species, remove any powers.
            $character->species = $request->species;
            $character->species_powers = [];
            $character->save();
        }

        if (0 === $character->species?->powers_choose) {
            return redirect()->route('stillfleet.create', 'attributes');
        }

        return new RedirectResponse(route('stillfleet.create', 'species-powers'));
    }

    public function saveSpeciesPowers(SpeciesPowersRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $characterId = $request->session()->get('stillfleet-partial');
        /** @var PartialCharacter $character */
        $character = PartialCharacter::where('_id', $characterId)
            ->where('owner', $user->email->address)
            ->firstOrFail();

        if (null === $character->species) {
            return redirect()
                ->route('stillfleet.create', 'species')
                ->withErrors(['species' => 'You must choose a species before powers.']);
        }

        $character->species_powers = $request->input('powers');
        $character->save();

        return new RedirectResponse(route('stillfleet.create', 'attributes'));
    }

    public function saveForLater(Request $request): RedirectResponse
    {
        $request->session()->forget('stillfleet-partial');

        return new RedirectResponse(route('dashboard'));
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
        /** @var ?PartialCharacter $character */
        $character = PartialCharacter::where('owner', $user->email->address)
            ->find($step);
        if (null !== $character) {
            $request->session()->put('stillfleet-partial', $character->id);
        }
        return $character;
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        /** @var User $user */
        $user = $request->user();

        return CharacterResource::collection(
            Character::where('owner', $user->email->address)->get()
        )
            ->additional(['links' => ['self' => route('stillfleet.characters.index')]]);
    }

    public function show(Request $request, Character $character): CharacterResource
    {
        /** @var User $user */
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
