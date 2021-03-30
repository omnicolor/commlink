<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\SlackLinkRequest;
use App\Models\SlackLink;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Http;
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
     * Given a Slack team ID, return the name of the team.
     * @param string $slackTeam
     * @return ?string
     */
    protected function getTeamName(string $slackTeam): ?string
    {
        $response = Http::withHeaders([
            'Authorization' => sprintf('Bearer %s', config('app.slack_token')),
        ])->get('https://slack.com/api/auth.teams.list');

        if ($response->failed() || $response['ok'] === false) {
            return null;
        }

        foreach ($response['teams'] as $team) {
            if ($team['id'] === $slackTeam) {
                return $team['name'];
            }
        }
        return null;
    }

    /**
     * Given a Slack User ID, return the user's name.
     * @param string $user
     * @return ?string
     */
    protected function getUserName(string $user): ?string
    {
        $response = Http::withHeaders([
            'Authorization' => sprintf('Bearer %s', config('app.slack_token')),
        ])->get(
            'https://slack.com/api/users.info',
            ['user' => urlencode($user)]
        );

        if ($response->failed() || $response['ok'] === false) {
            return null;
        }

        return $response['user']['name'];
    }

    /**
     * Handle a request to link a Slack team/user to the current user.
     * @param SlackLinkRequest $request
     * @return RedirectResponse
     */
    public function linkSlack(SlackLinkRequest $request): RedirectResponse
    {
        $team = $this->getTeamName($request->input('slack-team'));
        $user = $this->getUserName($request->input('slack-user'));

        SlackLink::create([
            'slack_team' => $request->input('slack-team'),
            'team_name' => $team,
            'slack_user' => $request->input('slack-user'),
            'user_name' => $user,
            // @phpstan-ignore-next-line
            'user_id' => \Auth::user()->id,
        ]);
        return redirect('settings')->with('success', 'Slack account linked.');
    }
}
