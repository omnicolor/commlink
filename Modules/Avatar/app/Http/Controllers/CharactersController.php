<?php

declare(strict_types=1);

namespace Modules\Avatar\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\View\View;
use League\CommonMark\CommonMarkConverter;
use Modules\Avatar\Http\Resources\CharacterResource;
use Modules\Avatar\Models\Character;

use function collect;
use function view;

/**
 * Controller for interacting with Avatar characters.
 */
class CharactersController extends Controller
{
    public function index(Request $request): JsonResource
    {
        return CharacterResource::collection(
            Character::where('owner', $request->user()?->email)->get()
        );
    }

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
        $converter = new CommonMarkConverter([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);

        $demeanors = collect($character->demeanors ?? []);
        $demeanor_options = collect($character->playbook->demeanor_options);
        $raw_description = $character->playbook->feature->description();
        $user = $request->user();
        return view(
            'avatar::character',
            [
                'character' => $character,
                'demeanor_options' => $demeanor_options,
                'demeanors' => $demeanors,
                'extra_demeanors' => $demeanors->diff($demeanor_options),
                'feature_description' => $converter->convert($raw_description),
                'moves' => collect($character->moves)->pluck('id'),
                'user' => $user,
            ]
        );
    }
}
