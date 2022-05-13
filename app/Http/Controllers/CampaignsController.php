<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Events\CampaignCreated;
use App\Http\Requests\CampaignCreateRequest;
use App\Models\Campaign;
use App\Models\Initiative;
use Gate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;

class CampaignsController extends Controller
{
    /**
     * Show the create campaign page.
     * @return View
     */
    public function createForm(): View
    {
        return view('campaign.create');
    }

    /**
     * Handle POST from the create campaign form.
     * @param CampaignCreateRequest $request
     * @return RedirectResponse
     */
    public function create(CampaignCreateRequest $request): RedirectResponse
    {
        $campaign = new Campaign($request->only([
            'description',
            'name',
            'system',
        ]));
        // @phpstan-ignore-next-line
        $campaign->registered_by = \Auth::user()->id;

        switch ($request->input('system')) {
            case 'cyberpunkred':
                $campaign->options = [
                    'nightCityTarot' => $request->boolean('night-city-tarot'),
                ];
                break;
            case 'shadowrun5e':
                $campaign->options = [
                    'creation' => (array)$request->input('sr5e-creation'),
                    'gameplay' => (string)$request->input('sr5e-gameplay'),
                    'rulesets' => (array)$request->input('sr5e-rules'),
                    'startDate' => (string)$request->input('sr5e-start-date'),
                ];
                break;
        }
        $campaign->save();
        CampaignCreated::dispatch($campaign);
        return redirect('dashboard');
    }

    /**
     * Handle a GET request to view the campaign.
     * @param Campaign $campaign
     * @return View
     */
    public function view(Campaign $campaign): View
    {
        Gate::authorize('view', $campaign);
        return view(
            'campaign.view',
            [
                'campaign' => $campaign,
                'user' => \Auth::user(),
            ]
        );
    }

    /**
     * Launch the GM screen.
     * @param Campaign $campaign
     * @return View
     */
    public function gmScreen(Campaign $campaign): View
    {
        Gate::authorize('gm', $campaign);
        switch ($campaign->system) {
            case 'cyberpunkred':
                return view(
                    'CyberpunkRed.gm-screen',
                    [
                        'campaign' => $campaign,
                        // @phpstan-ignore-next-line
                        'initiative' => Initiative::forCampaign($campaign)
                            ->orderByDesc('initiative')
                            ->get(),
                        'user' => \Auth::user(),
                    ]
                );
            default:
                return abort(Response::HTTP_NOT_FOUND);
        }
    }
}
