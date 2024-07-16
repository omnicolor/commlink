<?php

declare(strict_types=1);

namespace Modules\Alien\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Facades\App\Services\DiceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Modules\Alien\Http\Requests\CreateAttributesRequest;
use Modules\Alien\Http\Requests\CreateCareerRequest;
use Modules\Alien\Http\Requests\CreateFinishRequest;
use Modules\Alien\Http\Requests\CreateGearRequest;
use Modules\Alien\Http\Requests\CreateSkillsRequest;
use Modules\Alien\Http\Requests\CreateTalentRequest;
use Modules\Alien\Http\Resources\CharacterResource;
use Modules\Alien\Models\Armor;
use Modules\Alien\Models\Career;
use Modules\Alien\Models\Character;
use Modules\Alien\Models\Gear;
use Modules\Alien\Models\PartialCharacter;
use Modules\Alien\Models\Skill;
use Modules\Alien\Models\Weapon;
use RuntimeException;

use function abort;
use function abort_if;
use function array_key_exists;
use function collect;
use function count;
use function is_numeric;
use function redirect;
use function view;

class CharactersController extends Controller
{
    public const SESSION_KEY = 'alien-partial';

    public function create(
        Request $request,
        ?string $step = null,
    ): RedirectResponse | View {
        /** @var User */
        $user = $request->user();
        if ('save-for-later' === $step) {
            $request->session()->forget(self::SESSION_KEY);
            return new RedirectResponse(route('dashboard'));
        }
        if ('new' === $step) {
            $character = PartialCharacter::create(['owner' => $user->email]);
            $request->session()->put(self::SESSION_KEY, $character->id);
            return new RedirectResponse(route('alien.create', 'career'));
        }

        $character = $this->findPartialCharacter($request, $step);
        if (null !== $character && $step === $character->id) {
            return new RedirectResponse(route('alien.create', 'career'));
        }

        if (null === $character) {
            // No current character, see if they already have a character they
            // might want to continue.
            $characters = PartialCharacter::where('owner', $user->email)->get();

            if (0 !== count($characters)) {
                return view(
                    'alien::choose-character',
                    [
                        'characters' => $characters,
                        'user' => $user,
                    ],
                );
            }

            // No in-progress characters, create a new one.
            $character = PartialCharacter::create(['owner' => $user->email]);
            $request->session()->put(self::SESSION_KEY, $character->id);
        }

        if (null === $step || $step === $character->id) {
            $step = 'career';
        }

        switch ($step) {
            case 'attributes':
                return view(
                    'alien::create-attributes',
                    [
                        'agility' => $request->old('agility') ?? $character->agility_unmodified ?? '',
                        'career' => $character->career,
                        'character' => $character,
                        'creating' => 'attributes',
                        'empathy' => $request->old('empathy') ?? $character->empathy ?? '',
                        'strength' => $request->old('strength') ?? $character->strength ?? '',
                        'user' => $user,
                        'wits' => $request->old('wits') ?? $character->wits ?? '',
                    ]
                );
            case 'career':
                $name = $request->old('name') ?? $character->name;
                return view(
                    'alien::create-career',
                    [
                        'careers' => Career::all(),
                        'character' => $character,
                        'creating' => 'career',
                        'name' => $name,
                        'user' => $user,
                    ]
                );
            case 'finish':
                $agenda = $request->old('agenda') ?? $character->agenda;
                $appearance = $request->old('appearance') ?? $character->appearance;
                $buddy = $request->old('buddy') ?? $character->buddy;
                $rival = $request->old('rival') ?? $character->rival;
                return view(
                    'alien::create-finish',
                    [
                        'agenda' => $agenda,
                        'appearance' => $appearance,
                        'buddy' => $buddy,
                        'character' => $character,
                        'creating' => 'finish',
                        'rival' => $rival,
                        'user' => $user,
                    ]
                );
            case 'gear':
                if (null === $character->career) {
                    return redirect(route('alien.create', 'career'))
                        ->withErrors([
                            'error' => 'You must choose a career before you can select gear',
                        ]);
                }
                $gear = $character->career->gear;
                foreach ($gear as &$choice) {
                    /** @var array<string, string> $item */
                    foreach ($choice as &$item) {
                        try {
                            $item = new Gear($item['id']);
                            continue;
                        } catch (RuntimeException) {
                        }
                        try {
                            /** @psalm-suppress UndefinedMethod */
                            $item = new Weapon($item['id']);
                            continue;
                        } catch (RuntimeException) {
                        }
                        try {
                            /** @psalm-suppress UndefinedMethod */
                            $item = new Armor($item['id']);
                        } catch (RuntimeException) { // @codeCoverageIgnoreStart
                            Log::warning(
                                'Alien character has invalid item',
                                [
                                    'partial-character' => $character->id,
                                    /** @psalm-suppress UndefinedMethod */
                                    'item' => $item,
                                ],
                            );
                            // @codeCoverageIgnoreEnd
                        }
                    }
                }
                if (null !== $request->old('gear')) {
                    $chosenGear = $request->old('gear'); // @codeCoverageIgnore
                } else {
                    /** @var array<int, string> */
                    $chosenGear = collect($character->gear)->pluck('id')
                        ->merge(collect($character->weapons)->pluck('id'))
                        ->toArray();
                    if (null !== $character->armor) {
                        $chosenGear[] = $character->armor->id;
                    }
                }
                return view(
                    'alien::create-gear',
                    [
                        'character' => $character,
                        'chosenGear' => $chosenGear,
                        'creating' => 'gear',
                        'gear' => $gear,
                        'user' => $user,
                    ]
                );
            case 'review':
                return view(
                    'alien::character',
                    [
                        'character' => $character,
                        'creating' => 'review',
                        'user' => $request->user(),
                    ]
                );
            case 'skills':
                return view(
                    'alien::create-skills',
                    [
                        'career' => $character->career,
                        'character' => $character,
                        'creating' => 'skills',
                        'skills' => Skill::all(),
                        'user' => $user,
                    ]
                );
            case 'talent':
                if (null === $character->career) {
                    return redirect(route('alien.create', 'career'))
                        ->withErrors([
                            'error' => 'You must choose a career before you can choose a talent',
                        ]);
                }
                $chosenTalent = $request->old('talent');
                if (null === $chosenTalent && 0 !== count($character->talents)) {
                    $chosenTalent = $character->talents[0]->id;
                }
                return view(
                    'alien::create-talent',
                    [
                        'career' => $character->career,
                        'character' => $character,
                        'chosenTalent' => $chosenTalent,
                        'creating' => 'skills',
                        'talents' => $character->career->talents,
                        'user' => $user,
                    ]
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
        ?string $step
    ): ?PartialCharacter {
        /** @var User */
        $user = $request->user();

        // See if the user has already chosen to continue a character.
        $characterId = $request->session()->get(self::SESSION_KEY);

        if (null !== $characterId) {
            // Return the character they're working on.
            /** @var PartialCharacter */
            return PartialCharacter::where('owner', $user->email)
                ->where('_id', $characterId)
                ->firstOrFail();
        }
        if (null === $step) {
            return null;
        }

        // Maybe they're chosing to continue a character right now.
        /** @var PartialCharacter */
        $character = PartialCharacter::where('owner', $user->email)
            ->find($step);
        if (null !== $character) {
            $request->session()->put(self::SESSION_KEY, $character->id);
        }
        return $character;
    }

    public function saveCareer(CreateCareerRequest $request): RedirectResponse
    {
        /** @var User */
        $user = $request->user();
        $characterId = $request->session()->get(self::SESSION_KEY);
        $character = PartialCharacter::where('_id', $characterId)
            ->where('owner', $user->email)
            ->firstOrFail();

        $character->name = $request->name;
        $character->career = $request->career;
        $character->save();

        return new RedirectResponse(route('alien.create', 'attributes'));
    }

    public function saveAttributes(
        CreateAttributesRequest $request,
    ): RedirectResponse {
        /** @var User */
        $user = $request->user();
        $characterId = $request->session()->get(self::SESSION_KEY);
        $character = PartialCharacter::where('_id', $characterId)
            ->where('owner', $user->email)
            ->firstOrFail();

        $character->agility = (int)$request->agility;
        $character->empathy = (int)$request->empathy;
        $character->strength = (int)$request->strength;
        $character->wits = (int)$request->wits;
        $character->save();

        return new RedirectResponse(route('alien.create', 'skills'));
    }

    public function saveFinish(CreateFinishRequest $request): RedirectResponse
    {
        /** @var User */
        $user = $request->user();
        $characterId = $request->session()->get(self::SESSION_KEY);
        $character = PartialCharacter::where('_id', $characterId)
            ->where('owner', $user->email)
            ->firstOrFail();

        $character->agenda = $request->agenda;
        $character->appearance = $request->appearance;
        $character->buddy = $request->buddy;
        $character->rival = $request->rival;
        $character->save();

        return new RedirectResponse(route('alien.create', 'review'));
    }

    public function saveGear(CreateGearRequest $request): RedirectResponse
    {
        /** @var User */
        $user = $request->user();
        $characterId = $request->session()->get(self::SESSION_KEY);
        $character = PartialCharacter::where('_id', $characterId)
            ->where('owner', $user->email)
            ->firstOrFail();
        /** @var Career */
        $career = $character->career;

        $needsQuantity = [];
        foreach ($career->gear as $row) {
            /** @var array<string, int|string> $item */
            foreach ($row as $item) {
                if (array_key_exists('quantity', $item)) {
                    $needsQuantity[$item['id']] = $item['quantity'];
                }
            }
        }
        $gear = [];
        $armor = null;
        $weapons = [];
        foreach ($request->gear as $item) {
            try {
                $item = new Weapon($item);
                $weapons[] = $item;
                continue;
            } catch (RuntimeException) {
                // Ignore, the item may be armor or gear.
            }

            try {
                $item = new Armor($item);
                $armor = $item;
                continue;
            } catch (RuntimeException) {
                // Ignore, the item may be gear.
            }

            if (!array_key_exists($item, $needsQuantity)) {
                try {
                    $item = new Gear($item);
                    $gear[] = ['id' => $item->id];
                    continue;
                } catch (RuntimeException) { // @codeCoverageIgnore
                }
            }

            if (is_numeric($needsQuantity[$item])) {
                // Item has a certain number.
                // @codeCoverageIgnoreStart
                $gear[] = [
                    'id' => $item,
                    'quantity' => $needsQuantity[$item],
                ];
                continue;
                // @codeCoverageIgnoreEnd
            }
            // Item has a variable number or is invalid...
            /** @psalm-suppress UndefinedClass */
            $gear[] = [
                'id' => $item,
                'quantity' => DiceService::rollOne(6),
            ];
        }
        $character->armor = $armor;
        $character->gear = $gear;
        $character->weapons = $weapons;
        $character->save();

        return new RedirectResponse(route('alien.create', 'finish'));
    }

    public function saveSkills(CreateSkillsRequest $request): RedirectResponse
    {
        /** @var User */
        $user = $request->user();
        $characterId = $request->session()->get(self::SESSION_KEY);
        $character = PartialCharacter::where('_id', $characterId)
            ->where('owner', $user->email)
            ->firstOrFail();

        $characterSkills = $character->skills;
        $skills = collect(Skill::all())->pluck('id');
        foreach ($skills as $skill) {
            $characterSkills[$skill]->rank = (int)$request->input($skill);
        }
        $character->skills = $characterSkills;
        $character->save();

        return new RedirectResponse(route('alien.create', 'talent'));
    }

    public function saveTalent(CreateTalentRequest $request): RedirectResponse
    {
        /** @var User */
        $user = $request->user();
        $characterId = $request->session()->get(self::SESSION_KEY);
        $character = PartialCharacter::where('_id', $characterId)
            ->where('owner', $user->email)
            ->firstOrFail();

        $character->talents = [$request->talent];
        $character->save();

        return new RedirectResponse(route('alien.create', 'gear'));
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function index(Request $request): JsonResource
    {
        return CharacterResource::collection(
            // @phpstan-ignore-next-line
            Character::where('owner', $request->user()->email)->get()
        );
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function show(Request $request, Character $character): JsonResource
    {
        /** @var User */
        $user = $request->user();
        $campaign = $character->campaign();
        abort_if(
            $user->email !== $character->owner
            && (null === $campaign || $user->isNot($campaign->gamemaster)),
            Response::HTTP_NOT_FOUND
        );
        return new CharacterResource($character);
    }

    public function view(Request $request, Character $character): View
    {
        return view(
            'alien::character',
            [
                'character' => $character,
                'user' => $request->user(),
            ]
        );
    }
}
