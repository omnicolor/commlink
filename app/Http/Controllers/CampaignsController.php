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
        $filename = config('app.data_path.shadowrun5e') . 'rulebooks.php';
        $books = require $filename;
        return view('campaign.create', ['sr5eBooks' => $books]);
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
            $options = [
                'creation' => $request->input('sr5e-creation'),
                'gameplay' => $request->input('sr5e-gameplay'),
                'rulesets' => $request->input('sr5e-rules'),
                'startDate' => $request->input('sr5e-start-date'),
            ];
            $campaign->options = json_encode($options);
        }
        $campaign->save();
        return redirect('dashboard');
    }
}
