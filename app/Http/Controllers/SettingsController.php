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
     * Handle a request to link a Discord guild/user to the current Commlink
     * user.
     * @param LinkUserRequest $request
     * @return RedirectResponse
     */
    public function linkDiscord(LinkUserRequest $request): RedirectResponse
    {
        $serverId = $request->input('server-id');
        $remoteUserId = $request->input('user-id');

        // @phpstan-ignore-next-line
        $userId = \Auth::user()->id;

        $chatUser = ChatUser::where('server_id', $serverId)
            ->where('remote_user_id', $remoteUserId)
            ->where('user_id', $userId)
            ->where('server_type', ChatUser::TYPE_DISCORD)
            ->first();
        if (null !== $chatUser) {
            return redirect('settings')
                ->with('error', 'User already registered.')
                ->withInput();
        }

        $chatUser = new ChatUser([
            'server_id' => $serverId,
            'server_type' => ChatUser::TYPE_DISCORD,
            'remote_user_id' => $remoteUserId,
            'user_id' => $userId,
            'verified' => false,
        ]);
        $chatUser->server_name = $chatUser->getDiscordServerName($serverId);
        $chatUser->remote_user_name = $chatUser->getDiscordUserName($remoteUserId);
        $chatUser->save();

        return redirect('settings')
            ->with(
                'successObj',
                [
                    'id' => sprintf(
                        'success-discord-%s-%s',
                        $serverId,
                        $remoteUserId,
                    ),
                    'message' => sprintf(
                        'Discord account (%s - %s) linked.',
                        $chatUser->server_name,
                        $chatUser->remote_user_name,
                    ),
                ],
            );
    }

    /**
     * Handle a request to link a Slack team/user to the current Commlink user.
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
        if (null !== $chatUser) {
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

        return redirect('settings')
            ->with(
                'successObj',
                [
                    'id' => sprintf(
                        'success-slack-%s-%s',
                        $serverId,
                        $remoteUserId,
                    ),
                    'message' => sprintf(
                        'Slack account (%s - %s) linked.',
                        $chatUser->server_name,
                        $chatUser->remote_user_name,
                    ),
                ],
            );
    }

    /**
     * Handle a request to link a chat server to the current Commlink user.
     * @param LinkUserRequest $request
     * @return RedirectResponse
     */
    public function linkUser(LinkUserRequest $request): RedirectResponse
    {
        if ('T' === substr($request->input('server-id'), 0, 1)) {
            return $this->linkSlack($request);
        }
        return $this->linkDiscord($request);
    }
}
