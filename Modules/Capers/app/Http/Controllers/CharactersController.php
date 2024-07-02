<?php

declare(strict_types=1);

namespace Modules\Capers\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Modules\Capers\Http\Requests\AnchorsRequest;
use Modules\Capers\Http\Requests\BasicsRequest;
use Modules\Capers\Http\Requests\BoostsRequest;
use Modules\Capers\Http\Requests\GearRequest;
use Modules\Capers\Http\Requests\PowersRequest;
use Modules\Capers\Http\Requests\SkillsRequest;
use Modules\Capers\Http\Requests\TraitsRequest;
use Modules\Capers\Models\Character;
use Modules\Capers\Models\Gear;
use Modules\Capers\Models\PartialCharacter;
use Modules\Capers\Models\Power;

/**
 * Controller for interacting with Capers characters.
 * @psalm-suppress UnusedClass
 */
class CharactersController extends Controller
{
    /**
     * Show the selected step of character creation, or the first step if none
     * is explicitly chosen.
     */
    public function create(
        Request $request,
        ?string $step = null
    ): RedirectResponse | View {
        /** @var User */
        $user = Auth::user();

        if ('new' === $step) {
            /** @var PartialCharacter */
            $character = PartialCharacter::create([
                'owner' => $user->email,
            ]);
            $request->session()->put('capers-partial', $character->id);
            return view(
                'capers::create-basics',
                [
                    'background' => '',
                    'character' => $character,
                    'creating' => 'basics',
                    'description' => '',
                    'mannerisms' => '',
                    'name' => '',
                    'user' => $user,
                    'type' => null,
                ]
            );
        }

        $character = $this->findPartialCharacter($request, $step);
        if (null !== $character && $step === $character->id) {
            return new RedirectResponse('/characters/capers/create/basics');
        }
        if (null === $character) {
            // No current character, see if they already have a character they
            // might want to continue.
            $characters = PartialCharacter::where('owner', $user->email)->get();

            if (0 !== count($characters)) {
                return view(
                    'capers::choose-character',
                    [
                        'characters' => $characters,
                        'user' => $user,
                    ],
                );
            }

            // No in-progress characters, create a new one.
            /** @var PartialCharacter */
            $character = PartialCharacter::create(['owner' => $user->email]);
            $request->session()->put('capers-partial', $character->id);
        }

        if (null === $step || $step === $character->id) {
            $step = 'basics';
        }

        switch ($step) {
            case 'anchors':
                return view(
                    'capers::create-anchors',
                    [
                        'character' => $character,
                        'creating' => 'anchors',
                        'identity' => $request->old('identity') ?? optional($character->identity)->id ?? '',
                        'user' => $user,
                        'vice' => $request->old('vice') ?? optional($character->vice)->id ?? '',
                        'virtue' => $request->old('virtue') ?? optional($character->virtue)->id ?? '',
                    ]
                );
            case 'basics':
                return view(
                    'capers::create-basics',
                    [
                        'background' => $request->old('background') ?? $character->background ?? '',
                        'character' => $character,
                        'creating' => 'basics',
                        'description' => $request->old('description') ?? $character->description ?? '',
                        'mannerisms' => $request->old('mannerisms') ?? $character->mannerisms ?? '',
                        'name' => $request->old('name') ?? $character->name ?? '',
                        'user' => $user,
                        'type' => $request->old('type') ?? $character->type,
                    ]
                );
            case 'boosts':
                if (Character::TYPE_CAPER !== $character->type) {
                    return redirect(route('capers.create', 'basics'))
                        ->withErrors(['type' => 'Only Capers can choose boosts.']);
                }
                if (0 === count($character->powers)) {
                    return redirect(route('capers.create', 'powers'))
                        ->withErrors(['type' => 'You must choose powers before boosts.']);
                }

                $chosenBoosts = [];
                foreach ($character->powers as $power) {
                    foreach ($power->boosts as $boost) {
                        $chosenBoosts[] = sprintf('%s+%s', $power->id, $boost->id);
                    }
                }

                return view(
                    'capers::create-boosts',
                    [
                        'character' => $character,
                        'chosenBoosts' => $request->old('boosts') ?? $chosenBoosts,
                        'creating' => 'boosts',
                        'powers' => $character->powers,
                        'user' => $user,
                    ]
                );
            case 'gear':
                $money = 150.0;
                $gear = $character->gear;
                foreach ($gear as $item) {
                    $money -= $item->quantity * $item->cost;
                }
                $allGear = Gear::all();
                $types = array_unique(array_column((array)$allGear, 'type'));
                array_walk(
                    $types,
                    function (string &$value): void {
                        $value = ucfirst(str_replace('-', ' ', $value));
                    }
                );
                sort($types);
                return view(
                    'capers::create-gear',
                    [
                        'character' => $character,
                        'creating' => 'gear',
                        'gear' => $allGear,
                        'gearPurchased' => $gear,
                        'money' => $money,
                        'types' => $types,
                        'user' => $user,
                    ]
                );
            case 'review':
                return view(
                    'capers::character',
                    [
                        'character' => $character,
                        'creating' => 'review',
                        'user' => $user,
                    ]
                );
            case 'skills':
                return view(
                    'capers::create-skills',
                    [
                        'character' => $character,
                        'creating' => 'skills',
                        'skills' => array_keys((array)$character->skills),
                        'user' => $user,
                    ]
                );
            case 'perks':
                if (Character::TYPE_EXCEPTIONAL !== $character->type) {
                    return redirect(route('capers.create', 'basics'))
                        ->withErrors(['type' => 'Only Exceptionals can choose perks.']);
                }
                /** @psalm-suppress NoValue */
                return abort(
                    Response::HTTP_NOT_IMPLEMENTED,
                    'That step of character creation was not found.',
                );
            case 'powers':
                if (Character::TYPE_CAPER !== $character->type) {
                    return redirect(route('capers.create', 'basics'))
                        ->withErrors(['type' => 'Only Capers can choose powers.']);
                }
                $chosenPowers = [];
                foreach ($character->powers as $power) {
                    $chosenPowers[] = $power->id;
                }

                return view(
                    'capers::create-powers',
                    [
                        'character' => $character,
                        'chosenPowers' => $request->old('powers') ?? $chosenPowers,
                        'creating' => 'powers',
                        'major' => Power::major(),
                        'minor' => Power::minor(),
                        'options' => $request->old('options') ?? optional($character->meta)['powers-option'] ?? '',
                        'user' => $user,
                    ]
                );
            case 'traits':
                return view(
                    'capers::create-traits',
                    [
                        'character' => $character,
                        'creating' => 'traits',
                        'traitHigh' => $character->findAttributeAt(3),
                        'traitLow' => $character->findAttributeAt(1),
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
        $user = Auth::user();

        // See if the user has already chosen to continue a character.
        $characterId = $request->session()->get('capers-partial');

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
            $request->session()->put('capers-partial', $character->id);
        }
        return $character;
    }

    public function saveCharacter(Request $request): RedirectResponse
    {
        /** @var User */
        $user = Auth::user();
        /** @var string */
        $characterId = $request->session()->pull('capers-partial');
        /** @var PartialCharacter */
        $partialCharacter = PartialCharacter::where('_id', $characterId)
            ->where('owner', $user->email)
            ->firstOrFail();
        $character = $partialCharacter->toCharacter();
        $character->save();
        $partialCharacter->delete();

        return new RedirectResponse(route('capers.character', $character));
    }

    public function storeAnchors(AnchorsRequest $request): RedirectResponse
    {
        /** @var User */
        $user = Auth::user();
        /** @var string */
        $characterId = $request->session()->get('capers-partial');
        /** @var PartialCharacter */
        $character = PartialCharacter::where('_id', $characterId)
            ->where('owner', $user->email)
            ->firstOrFail();

        $character->identity = $request->input('identity');
        $character->vice = $request->input('vice');
        $character->virtue = $request->input('virtue');
        $character->update();

        return new RedirectResponse(route('capers.create', $request->input('nav')));
    }

    public function storeBasics(BasicsRequest $request): RedirectResponse
    {
        $characterId = $request->session()->get('capers-partial');
        /** @var User */
        $user = Auth::user();
        /** @var PartialCharacter */
        $character = PartialCharacter::where('_id', $characterId)
            ->where('owner', $user->email)
            ->firstOrFail();

        $character->background = $request->input('background');
        $character->description = $request->input('description');
        $character->mannerisms = $request->input('mannerisms');
        $character->name = $request->input('name');
        $character->type = $request->input('type');
        $character->update();

        return new RedirectResponse(route('capers.create', $request->input('nav')));
    }

    /**
     * @psalm-suppress InvalidPropertyAssignmentValue
     */
    public function storeBoosts(BoostsRequest $request): RedirectResponse
    {
        /** @var User */
        $user = Auth::user();

        $characterId = $request->session()->get('capers-partial');
        /** @var PartialCharacter */
        $character = PartialCharacter::where('_id', $characterId)
            ->where('owner', $user->email)
            ->firstOrFail();
        if (Character::TYPE_CAPER !== $character->type) {
            return redirect('/characters/capers/create/skills')
                ->withErrors(['type' => 'Only Capers can choose powers.']);
        }

        $powers = [];
        foreach ($character->powers as $power) {
            $powers[$power->id] = [
                'boosts' => [],
                'id' => $power->id,
                'rank' => $power->rank,
            ];
        }
        foreach ($request->input('boosts') as $boost) {
            [$powerId, $boostId] = explode('+', (string) $boost);
            $powers[$powerId]['boosts'][] = $boostId;
        }
        // @phpstan-ignore-next-line
        $character->powers = $powers;
        $character->update();

        return new RedirectResponse(sprintf(
            '/characters/capers/create/%s',
            $request->input('nav')
        ));
    }

    /**
     * @psalm-suppress InvalidPropertyAssignmentValue
     */
    public function storeGear(GearRequest $request): RedirectResponse
    {
        $characterId = $request->session()->get('capers-partial');
        /** @var User */
        $user = Auth::user();

        $character = PartialCharacter::where('_id', $characterId)
            ->where('owner', $user->email)
            ->firstOrFail();

        $gearIds = $request->input('gear', []);
        $gearQuantities = $request->input('quantity', []);
        $gear = [];
        foreach ($gearIds as $key => $id) {
            if (!isset($gearQuantities[$key]) || 1 > (int)$gearQuantities[$key]) {
                continue;
            }

            $gear[] = [
                'id' => $id,
                'quantity' => (int)$gearQuantities[$key],
            ];
        }
        // @phpstan-ignore-next-line
        $character->gear = $gear;

        $character->update();

        return new RedirectResponse(sprintf(
            '/characters/capers/create/%s',
            $request->input('nav')
        ));
    }

    public function storePowers(PowersRequest $request): RedirectResponse
    {
        $characterId = $request->session()->get('capers-partial');
        /** @var User */
        $user = Auth::user();

        /** @var PartialCharacter */
        $character = PartialCharacter::where('_id', $characterId)
            ->where('owner', $user->email)
            ->firstOrFail();
        if (Character::TYPE_CAPER !== $character->type) {
            return redirect('/characters/capers/create/skills')
                ->withErrors(['type' => 'Only Capers can choose powers.']);
        }
        $rank = 1;
        if ('one-minor' === $request->input('options')) {
            $rank = 2;
        }
        $powers = [];
        foreach ($request->input('powers') as $powerId) {
            $powers[] = [
                'id' => $powerId,
                'rank' => $rank,
                'boosts' => [],
            ];
        }
        $character->powers = $powers;

        $meta = $character->meta ?? [];
        $meta['powers-option'] = $request->input('options');
        $character->meta = $meta;

        $character->update();

        return new RedirectResponse(sprintf(
            '/characters/capers/create/%s',
            $request->input('nav')
        ));
    }

    public function storeSkills(SkillsRequest $request): RedirectResponse
    {
        $characterId = $request->session()->get('capers-partial');
        /** @var User */
        $user = Auth::user();

        /** @var PartialCharacter */
        $character = PartialCharacter::where('_id', $characterId)
            ->where('owner', $user->email)
            ->firstOrFail();
        $character->skills = $request->input('skills');
        $character->update();

        return new RedirectResponse(sprintf(
            '/characters/capers/create/%s',
            $request->input('nav')
        ));
    }

    public function storeTraits(TraitsRequest $request): RedirectResponse
    {
        $characterId = $request->session()->get('capers-partial');
        /** @var User */
        $user = Auth::user();

        $character = PartialCharacter::where('_id', $characterId)
            ->where('owner', $user->email)
            ->firstOrFail();
        $attributes = [
            'agility' => 2,
            'charisma' => 2,
            'expertise' => 2,
            'perception' => 2,
            'resilience' => 2,
            'strength' => 2,
        ];
        $attributes[$request->input('trait-high')]++;
        $attributes[$request->input('trait-low')]--;
        $character->fill($attributes)->save();

        return new RedirectResponse(sprintf(
            '/characters/capers/create/%s',
            $request->input('nav')
        ));
    }

    public function view(Character $character): View
    {
        $user = Auth::user();
        return view(
            'capers::character',
            ['character' => $character, 'user' => $user]
        );
    }
}
