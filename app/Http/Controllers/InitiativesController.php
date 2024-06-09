<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Events\InitiativeAdded;
use App\Http\Requests\InitiativeCreateRequest;
use App\Models\Campaign;
use App\Models\Initiative;
use Facades\App\Services\DiceService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use function now;

/**
 * @psalm-suppress UnusedClass
 */
class InitiativesController extends Controller
{
    public function destroy(Campaign $campaign, Initiative $initiative): Response
    {
        $this->authorize('gm', $campaign);
        abort_if(
            $campaign->id !== $initiative->campaign_id,
            Response::HTTP_FORBIDDEN,
            'Requested initiative is for a different campaign'
        );
        $initiative->delete();
        return new Response('', Response::HTTP_NO_CONTENT);
    }

    public function index(Campaign $campaign): Response
    {
        $this->authorize('gm', $campaign);
        $initiatives = Initiative::forCampaign($campaign)->get();
        return new Response(['initiatives' => $initiatives]);
    }

    public function update(
        Campaign $campaign,
        Initiative $initiative,
        Request $request
    ): Response {
        $this->authorize('gm', $campaign);
        abort_if(
            $campaign->id !== $initiative->campaign_id,
            Response::HTTP_FORBIDDEN,
            'Requested initiative is for a different campaign'
        );
        $initiative->update($request->only(['initiative', 'character_name']));
        $initiative->updated_at = now();
        $initiative->save();
        return new Response(['initiative' => $initiative]);
    }

    public function show(Campaign $campaign, Initiative $initiative): Response
    {
        $this->authorize('gm', $campaign);
        abort_if(
            $campaign->id !== $initiative->campaign_id,
            Response::HTTP_FORBIDDEN,
            'Requested initiative is for a different campaign'
        );
        return new Response(['initiative' => $initiative]);
    }

    /**
     * @psalm-suppress UndefinedClass
     */
    public function store(
        Campaign $campaign,
        InitiativeCreateRequest $request,
    ): Response {
        $this->authorize('gm', $campaign);

        if ($request->has('initiative')) {
            $score = $request->initiative;
        } else {
            $score = $request->base_initiative;
            for ($i = 1; $i <= $request->initiative_dice; $i++) {
                $score += DiceService::rollOne(6);
            }
        }

        $initiative = Initiative::create([
            'campaign_id' => $campaign->id,
            'character_name' => $request->character_name,
            'initiative' => $score,
            'grunt_id' => $request->grunt_id,
        ]);
        $initiative->refresh();
        InitiativeAdded::dispatch($initiative, $campaign, null);
        return new Response(
            ['initiative' => $initiative],
            Response::HTTP_CREATED
        );
    }

    public function truncate(Campaign $campaign): Response
    {
        $this->authorize('gm', $campaign);
        Initiative::forCampaign($campaign)->delete();
        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
