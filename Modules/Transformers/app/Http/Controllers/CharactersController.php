<?php

declare(strict_types=1);

namespace Modules\Transformers\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Modules\Transformers\Http\Requests\BaseRequest;
use Modules\Transformers\Http\Requests\ProgrammingRequest;
use Modules\Transformers\Http\Requests\StatisticsRequest;
use Modules\Transformers\Models\Character;
use Modules\Transformers\Models\PartialCharacter;
use Modules\Transformers\Models\Programming;

/** @psalm-suppress UnusedClass */
class CharactersController extends Controller
{
    protected const SESSION_KEY = 'transformers-partial';

    public function create(
        Request $request,
        ?string $step = null,
    ): RedirectResponse | View {
        /** @var User */
        $user = Auth::user();

        if ('new' === $step) {
            /** @var PartialCharacter */
            $character = PartialCharacter::create(['owner' => $user->email]);
            $request->session()->put(self::SESSION_KEY, $character->id);
            return new RedirectResponse('/characters/transformers/create/base');
        }

        $character = $this->findPartialCharacter($request, $step);
        if (null !== $character && $step === $character->id) {
            return new RedirectResponse('/characters/transformers/create/base');
        }
        if (null === $character) {
            // No current character, see if they already have a character they
            // might want to continue.
            $characters = PartialCharacter::where('owner', $user->email)->get();

            if (0 !== count($characters)) {
                return view(
                    'transformers::choose-character',
                    [
                        'characters' => $characters,
                        'user' => $user,
                    ],
                );
            }

            // No in-progress characters, create a new one.
            /** @var PartialCharacter */
            $character = PartialCharacter::create(['owner' => $user->email]);
            $request->session()->put(self::SESSION_KEY, $character->id);
        }

        if (null === $step || $step === $character->id) {
            $step = 'base';
        }

        switch ($step) {
            case 'alt-mode':
                if (null === $character->endurance_robot) {
                    return redirect(route('transformers.create', 'statistics'))
                        ->withErrors('You must roll statistics before choosing your alt mode');
                }
                return view(
                    'transformers::create-alt-mode',
                    [
                        'character' => $character,
                        'creating' => 'alt-mode',
                    ]
                );
            case 'base':
                return view(
                    'transformers::create-base',
                    [
                        'allegiance' => $request->old('allegiance')
                            ?? $character->allegiance ?? null,
                        'character' => $character,
                        'creating' => 'base',
                        'name' => $request->old('name') ?? $character->name ?? '',
                        'color_primary' => $request->old('color_primary')
                            ?? $character->color_primary ?? '',
                        'color_secondary' => $request->old('color_secondary')
                            ?? $character->color_secondary ?? '',
                        'quote' => $request->old('quote') ?? $character->quote ?? '',
                    ]
                );
            case 'function':
                // @phpstan-ignore-next-line
                $programming = Programming::tryFrom((string)$request->old('programming', '') ?? '')
                    ?? $character->programming;
                return view(
                    'transformers::create-function',
                    [
                        'character' => $character,
                        'creating' => 'function',
                        'programming' => $programming,
                    ]
                );
            case 'statistics':
                return view(
                    'transformers::create-statistics',
                    [
                        'character' => $character,
                        'courage_robot' => $request->old('courage_robot')
                            ?? $character->courage_robot ?? '',
                        'creating' => 'statistics',
                        'endurance_robot' => $request->old('endurance_robot')
                            ?? $character->endurance_robot ?? '',
                        'firepower_robot' => $request->old('firepower_robot')
                            ?? $character->firepower_robot ?? '',
                        'intelligence_robot' => $request->old('intelligence_robot')
                            ?? $character->intelligence_robot ?? '',
                        'rank_robot' => $request->old('rank_robot')
                            ?? $character->rank_robot ?? '',
                        'skill_robot' => $request->old('skill_robot')
                            ?? $character->skill_robot ?? '',
                        'speed_robot' => $request->old('speed_robot')
                            ?? $character->speed_robot ?? '',
                        'strength_robot' => $request->old('strength_robot')
                            ?? $character->strength_robot ?? '',
                    ]
                );
            case 'save-for-later':
                return $this->saveForLater($request);
            default:
                /** @psalm-suppress NoValue */
                return abort(
                    Response::HTTP_NOT_FOUND,
                    'That step of character creation was not found.',
                );
        }
    }

    public function createBase(BaseRequest $request): RedirectResponse
    {
        $character_id = $request->session()->get(self::SESSION_KEY);

        /** @var User */
        $user = Auth::user();

        /** @var PartialCharacter */
        $character = PartialCharacter::where('_id', $character_id)
            ->where('owner', $user->email)
            ->firstOrFail();

        $character->fill($request->validated());
        $character->updated_at = (string)now();
        $character->save();

        return new RedirectResponse(route('transformers.create', 'statistics'));
    }

    public function createProgramming(ProgrammingRequest $request): RedirectResponse
    {
        $character_id = $request->session()->get(self::SESSION_KEY);

        /** @var User */
        $user = Auth::user();

        /** @var PartialCharacter */
        $character = PartialCharacter::where('_id', $character_id)
            ->where('owner', $user->email)
            ->firstOrFail();

        $character->programming = $request->input('programming');
        $character->updated_at = (string)now();
        $character->save();

        return new RedirectResponse(route('transformers.create', 'alt-mode'));
    }

    public function createStatistics(StatisticsRequest $request): RedirectResponse
    {
        $character_id = $request->session()->get(self::SESSION_KEY);

        /** @var User */
        $user = Auth::user();

        /** @var PartialCharacter */
        $character = PartialCharacter::where('_id', $character_id)
            ->where('owner', $user->email)
            ->firstOrFail();

        $character->fill($request->validated());
        $character->updated_at = (string)now();
        $character->save();

        return new RedirectResponse(route('transformers.create', 'function'));
    }

    protected function saveForLater(Request $request): RedirectResponse
    {
        $request->session()->forget(self::SESSION_KEY);
        return new RedirectResponse(route('dashboard'));
    }

    protected function findPartialCharacter(
        Request $request,
        ?string $step
    ): ?PartialCharacter {
        /** @var User */
        $user = Auth::user();

        // See if the user has already chosen to continue a character.
        $character_id = $request->session()->get(self::SESSION_KEY);

        if (null !== $character_id) {
            // Return the character they're working on.
            /** @var PartialCharacter */
            return PartialCharacter::where('owner', $user->email)
                ->where('_id', $character_id)
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

    /**
     * View a character's sheet.
     */
    public function view(Character $character): View
    {
        $user = Auth::user();
        return view(
            'transformers::character',
            ['character' => $character, 'user' => $user]
        );
    }
}
