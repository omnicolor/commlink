<?php

declare(strict_types=1);

namespace Modules\Alien\Http\Controllers;

use App\Events\RollEvent;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WebChannel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Modules\Alien\Http\Requests\RollRequest;
use Modules\Alien\Http\Resources\RollResource;
use Modules\Alien\Models\Character;
use Modules\Alien\Rolls\Skill;

use function sprintf;

/**
 * @psalm-api
 */
class RollController extends Controller
{
    public function show(
        Request $request,
        string $id
    ): JsonResponse | RollResource {
        $roll = Cache::get('roll:' . $id);
        abort_if(
            null === $roll,
            JsonResponse::HTTP_NOT_FOUND,
            'The requested roll was not found',
        );
        /** @var User */
        $user = $request->user();
        /** @var Character */
        $character = Character::where('_id', $roll['character'])
            ->where('owner', $user->email)
            ->first();
        abort_if(
            null === $character,
            JsonResponse::HTTP_NOT_FOUND,
            'The requested roll was not found',
        );
        return new RollResource($request, $roll, $character);
    }

    public function store(RollRequest $request): JsonResponse | RollResource
    {
        /** @var User */
        $user = $request->user();
        /** @var Character */
        $character = Character::where('_id', $request->character)
            ->where('owner', $user->email)
            ->firstOrFail();
        $channel = new WebChannel([
            'campaign_id' => $character->campaign_id,
        ]);
        $channel->setCharacter($character);
        $roll = new Skill(
            sprintf('skill %s', $request->skill),
            $user->name,
            $channel,
        );
        $result = $roll->forWeb();
        $result['id'] = (string)Str::uuid();
        $result['created_at'] = now()->toAtomString();
        $result['character'] = $request->character;
        Cache::put('roll:' . $result['id'], $result, 10 * 60);
        RollEvent::dispatch($roll, $channel);
        return new RollResource($request, $result, $character);
    }
}
