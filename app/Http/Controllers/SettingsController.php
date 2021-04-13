<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\LinkUserRequest;
use App\Models\ChatUser;
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
     * @param LinkUserRequest $request
     * @return RedirectResponse
     */
    protected function linkSlack(LinkUserRequest $request): RedirectResponse
    {
        $serverId = $request->input('server-id');
        $remoteUserId = $request->input('user-id');
        // @phpstan-ignore-next-line
        $userId = \Auth::user()->id;

        $chatUser = ChatUser::where('server_id', $serverId)
            ->where('remote_user_id', $remoteUserId)
            ->where('user_id', $userId)
            ->where('server_type', ChatUser::TYPE_SLACK)
            ->first();
        if (!is_null($chatUser)) {
            return redirect('settings')
                ->with('error', 'User already registered.')
                ->withInput();
        }

        $chatUser = new ChatUser([
            'server_id' => $serverId,
            'server_type' => ChatUser::TYPE_SLACK,
            'remote_user_id' => $remoteUserId,
            'user_id' => $userId,
            'verified' => false,
        ]);
        $chatUser->server_name = $chatUser->getSlackTeamName($serverId);
        $chatUser->remote_user_name
            = $chatUser->getSlackUserName($remoteUserId);
        $chatUser->save();

        return redirect('settings')->with('success', 'Slack account linked.');
    }

    /**
     * Handle a request to link a chat server to the current user.
     * @param LinkUserRequest $request
     * @return RedirectResponse
     */
    public function linkUser(LinkUserRequest $request): RedirectResponse
    {
        if ('slack' === $request->input('server-type')) {
            return $this->linkSlack($request);
        }
        return redirect('settings')
            ->with('error', 'Discord isn\'t ready.')
            ->withInput();
    }
}
