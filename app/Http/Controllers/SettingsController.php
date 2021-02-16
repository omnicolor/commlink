<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\SlackLinkRequest;
use App\Models\SlackLink;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingsController extends Controller
{
    /**
     * Show the settings page.
     * @return View
     */
    public function show(): View
    {
        return view('settings', ['user' => \Auth::user()]);
    }

    /**
     * Handle a request to link a Slack team/user to the current user.
     * @param SlackLinkRequest $request
     * @return RedirectResponse
     */
    public function linkSlack(SlackLinkRequest $request): RedirectResponse
    {
        SlackLink::create([
            'slack_team' => $request->input('slack-team'),
            'slack_user' => $request->input('slack-user'),
            // @phpstan-ignore-next-line
            'user_id' => \Auth::user()->id,
        ]);
        return redirect('settings')->with('success', 'Slack account linked.');
    }
}
