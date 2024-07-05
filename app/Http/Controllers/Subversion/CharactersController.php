<?php

declare(strict_types=1);

namespace App\Http\Controllers\Subversion;

use App\Http\Controllers\Controller;
use App\Http\Requests\Subversion\CreateBackgroundRequest;
use App\Http\Requests\Subversion\CreateCasteRequest;
use App\Http\Requests\Subversion\CreateHooksRequest;
use App\Http\Requests\Subversion\CreateIdeologyRequest;
use App\Http\Requests\Subversion\CreateImpulseRequest;
use App\Http\Requests\Subversion\CreateLineageRequest;
use App\Http\Requests\Subversion\CreateOriginRequest;
use App\Http\Requests\Subversion\CreateRelationsRequest;
use App\Http\Requests\Subversion\CreateValuesRequest;
use App\Models\Subversion\Background;
use App\Models\Subversion\Caste;
use App\Models\Subversion\Character;
use App\Models\Subversion\Ideology;
use App\Models\Subversion\Impulse;
use App\Models\Subversion\Lineage;
use App\Models\Subversion\Origin;
use App\Models\Subversion\PartialCharacter;
use App\Models\Subversion\Relation;
use App\Models\Subversion\RelationArchetype;
use App\Models\Subversion\RelationAspect;
use App\Models\Subversion\RelationLevel;
use App\Models\Subversion\Skill;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

use function abort;
use function count;
use function route;
use function usort;
use function view;

/**
 * @psalm-suppress UnusedClass
 */
class CharactersController extends Controller
{
    /**
     * @psalm-suppress PossiblyUnusedParam
     */
    public function create(
        Request $request,
        ?string $step = null,
    ): RedirectResponse | View {
        /** @var User */
        $user = $request->user();

        if ('new' === $step) {
            /** @var PartialCharacter */
            $character = PartialCharacter::create(['owner' => $user->email]);
            $request->session()->put('subversion-partial', $character->id);
            return view(
                'Subversion.create-lineage',
                [
                    'character' => $character,
                    'creating' => 'lineage',
                    'lineageId' => $character->lineage?->id,
                    'lineageOptionId' => $character->lineage?->option?->id,
                    'lineages' => Lineage::all(),
                    'user' => $user,
                ],
            );
        }

        if ('later' === $step) {
            $request->session()->forget('subversion-partial');
            $characters = PartialCharacter::where('owner', $user->email)->get();
            return view(
                'Subversion.choose-character',
                [
                    'characters' => $characters,
                    'user' => $user,
                ],
            );
        }

        $character = $this->findPartialCharacter($request, $step);
        if (null !== $character && $step === $character->id) {
            return new RedirectResponse('/characters/subversion/create/lineage');
        }
        if (null === $character) {
            // No current character, see if they already have a character they
            // might want to continue.
            $characters = PartialCharacter::where('owner', $user->email)->get();

            if (0 !== count($characters)) {
                return view(
                    'Subversion.choose-character',
                    [
                        'characters' => $characters,
                        'user' => $user,
                    ],
                );
            }

            // No in-progress characters, create a new one.
            /** @var PartialCharacter */
            $character = PartialCharacter::create(['owner' => $user->email]);
            $request->session()->put('subversion-partial', $character->id);
        }

        if (null === $step || $step === $character->id) {
            $step = 'lineage';
        }

        switch ($step) {
            case 'background':
                return view(
                    'Subversion.create-background',
                    [
                        'backgroundId' => $character->background?->id,
                        'backgrounds' => Background::all(),
                        'character' => $character,
                        'creating' => 'background',
                        'user' => $user,
                    ],
                );
            case 'caste':
                return view(
                    'Subversion.create-caste',
                    [
                        'casteId' => $character->caste?->id,
                        'castes' => Caste::all(),
                        'character' => $character,
                        'creating' => 'caste',
                        'user' => $user,
                    ],
                );
            case 'hooks':
                return view(
                    'Subversion.create-hooks',
                    [
                        'hooks' => $character->hooks ?? [],
                        'character' => $character,
                        'creating' => 'hooks',
                        'user' => $user,
                    ],
                );
            case 'ideology':
                return view(
                    'Subversion.create-ideology',
                    [
                        'ideologyId' => $character->ideology?->id,
                        'ideologies' => Ideology::all(),
                        'character' => $character,
                        'creating' => 'ideology',
                        'user' => $user,
                    ],
                );
            case 'impulse':
                return view(
                    'Subversion.create-impulse',
                    [
                        'impulseId' => $character->impulse?->id,
                        'impulses' => Impulse::all(),
                        'character' => $character,
                        'creating' => 'impulse',
                        'user' => $user,
                    ],
                );
            case 'lineage':
                return view(
                    'Subversion.create-lineage',
                    [
                        'character' => $character,
                        'creating' => 'lineage',
                        'lineageId' => $character->lineage?->id,
                        'lineageOptionId' => $character->lineage?->option?->id,
                        'lineages' => Lineage::all(),
                        'user' => $user,
                    ],
                );
            case 'origin':
                return view(
                    'Subversion.create-origin',
                    [
                        'character' => $character,
                        'creating' => 'origin',
                        'originId' => $character->origin?->id,
                        'origins' => Origin::all(),
                        'user' => $user,
                    ],
                );
            case 'relations':
                $levels = RelationLevel::all();
                usort($levels, [RelationLevel::class, 'sort']);

                return view(
                    'Subversion.create-relations',
                    [
                        'archetypes' => RelationArchetype::all(),
                        'aspects' => RelationAspect::all(),
                        'character' => $character,
                        'creating' => 'relations',
                        'levels' => $levels,
                        'skills' => Skill::all(),
                        'user' => $user,
                    ],
                );
            case 'values':
                return view(
                    'Subversion.create-values',
                    [
                        'character' => $character,
                        'creating' => 'values',
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
        ?string $step
    ): ?PartialCharacter {
        /** @var User */
        $user = Auth::user();

        // See if the user has already chosen to continue a character.
        $characterId = $request->session()->get('subversion-partial');

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
            $request->session()->put('subversion-partial', $character->id);
        }
        return $character;
    }

    public function storeBackground(CreateBackgroundRequest $request): RedirectResponse
    {
        /** @var User */
        $user = $request->user();
        /** @var string */
        $characterId = $request->session()->get('subversion-partial');

        /** @var PartialCharacter */
        $character = PartialCharacter::where('_id', $characterId)
            ->where('owner', $user->email)
            ->firstOrFail();
        $character->background = $request->background;
        $character->update();

        return new RedirectResponse(route('subversion.create', 'caste'));
    }

    public function storeCaste(CreateCasteRequest $request): RedirectResponse
    {
        /** @var User */
        $user = $request->user();
        /** @var string */
        $characterId = $request->session()->get('subversion-partial');

        /** @var PartialCharacter */
        $character = PartialCharacter::where('_id', $characterId)
            ->where('owner', $user->email)
            ->firstOrFail();
        $character->caste = $request->caste;
        $character->update();

        return new RedirectResponse(route('subversion.create', 'ideology'));
    }

    public function storeHooks(CreateHooksRequest $request): RedirectResponse
    {
        /** @var User */
        $user = $request->user();
        /** @var string */
        $characterId = $request->session()->get('subversion-partial');

        /** @var PartialCharacter */
        $character = PartialCharacter::where('_id', $characterId)
            ->where('owner', $user->email)
            ->firstOrFail();
        $character->hooks = [
            $request->hook1,
            $request->hook2,
        ];
        $character->update();

        return new RedirectResponse(route('subversion.create', 'relations'));
    }

    public function storeIdeology(CreateIdeologyRequest $request): RedirectResponse
    {
        /** @var User */
        $user = $request->user();
        /** @var string */
        $characterId = $request->session()->get('subversion-partial');

        /** @var PartialCharacter */
        $character = PartialCharacter::where('_id', $characterId)
            ->where('owner', $user->email)
            ->firstOrFail();
        $character->ideology = $request->ideology;
        $character->update();

        return new RedirectResponse(route('subversion.create', 'values'));
    }

    public function storeImpulse(CreateImpulseRequest $request): RedirectResponse
    {
        /** @var User */
        $user = $request->user();
        /** @var string */
        $characterId = $request->session()->get('subversion-partial');

        /** @var PartialCharacter */
        $character = PartialCharacter::where('_id', $characterId)
            ->where('owner', $user->email)
            ->firstOrFail();
        $character->impulse = $request->impulse;
        $character->update();

        return new RedirectResponse(route('subversion.create', 'hooks'));
    }

    public function storeLineage(CreateLineageRequest $request): RedirectResponse
    {
        /** @var User */
        $user = $request->user();
        /** @var string */
        $characterId = $request->session()->get('subversion-partial');

        /** @var PartialCharacter */
        $character = PartialCharacter::where('_id', $characterId)
            ->where('owner', $user->email)
            ->firstOrFail();
        $character->name = $request->name;
        $character->lineage = $request->lineage;
        $character->lineage_option = $request->option;
        $character->update();

        return new RedirectResponse(route('subversion.create', 'origin'));
    }

    public function storeOrigin(CreateOriginRequest $request): RedirectResponse
    {
        /** @var User */
        $user = $request->user();
        /** @var string */
        $characterId = $request->session()->get('subversion-partial');

        /** @var PartialCharacter */
        $character = PartialCharacter::where('_id', $characterId)
            ->where('owner', $user->email)
            ->firstOrFail();
        $character->origin = $request->origin;
        $character->update();

        return new RedirectResponse(route('subversion.create', 'background'));
    }

    public function storeRelations(CreateRelationsRequest $request): RedirectResponse
    {
        $relations = [];
        foreach ($request->relation_name as $key => $name) {
            $additional = $request->relation_category[$key];
            if (null !== $additional) {
                $additional = explode(',', $additional);
            } else {
                $additional = [];
            }
            $archetypes = [];
            foreach (explode(',', $request->relation_archetype[$key]) as $index => $archetype) {
                $category = null;
                if (array_key_exists($index, $additional)) {
                    $category = $additional[$index];
                }
                $archetypes[] = new RelationArchetype($archetype, $category);
            }
            $aspects = [];
            if (null !== $request->relation_aspects[$key]) {
                foreach (explode(',', $request->relation_aspects[$key]) as $aspect) {
                    $aspects[] = new RelationAspect($aspect);
                }
            }
            $skills = [];
            foreach (explode(',', $request->relation_skill[$key]) as $skill) {
                $skills[] = new Skill($skill);
            }
            $level = new RelationLevel($request->relation_level[$key]);
            $faction = 'true' === $request->relation_faction[$key];
            $relations[] = (new Relation(
                archetypes: $archetypes,
                aspects: $aspects,
                faction: $faction,
                level: $level,
                name: $name,
                notes: $request->relation_notes[$key],
                power: $level->power,
                regard: $level->regard,
                skills: $skills,
            ))->toArray();
        }

        /** @var User */
        $user = $request->user();
        /** @var string */
        $characterId = $request->session()->get('subversion-partial');
        /** @var PartialCharacter */
        $character = PartialCharacter::where('_id', $characterId)
            ->where('owner', $user->email)
            ->firstOrFail();
        $character->relations = $relations;
        $character->update();

        return new RedirectResponse(route('subversion.create', 'debts'));
    }

    public function storeValues(CreateValuesRequest $request): RedirectResponse
    {
        /** @var User */
        $user = $request->user();
        /** @var string */
        $characterId = $request->session()->get('subversion-partial');

        /** @var PartialCharacter */
        $character = PartialCharacter::where('_id', $characterId)
            ->where('owner', $user->email)
            ->firstOrFail();
        $character->values = [
            $request->value1,
            $request->value2,
            $request->value3,
        ];
        $character->corrupted_value = (bool)$request->corrupted;
        $character->update();

        return new RedirectResponse(route('subversion.create', 'impulse'));
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
