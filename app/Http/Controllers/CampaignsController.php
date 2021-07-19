<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CampaignCreateRequest;
use App\Models\Campaign;
use Illuminate\Http\RedirectResponse;
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
        $campaign = Campaign::make($request->only([
            'description',
            'name',
            'system',
        ]));
        // @phpstan-ignore-next-line
        $campaign->registered_by = \Auth::user()->id;
        if ('shadowrun5e' === $request->input('system')) {
            $campaign->options = [
                'creation' => (array)$request->input('sr5e-creation'),
                'gameplay' => (string)$request->input('sr5e-gameplay'),
                'rulesets' => (array)$request->input('sr5e-rules'),
                'startDate' => (string)$request->input('sr5e-start-date'),
            ];
        }
        $campaign->save();
        return redirect('dashboard');
    }
}
