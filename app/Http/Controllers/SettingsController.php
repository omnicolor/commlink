<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\LinkUserRequest;
use App\Models\ChatUser;
use App\Models\Traits\InteractsWithDiscord;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SettingsController extends Controller
{
    use InteractsWithDiscord;

    /**
     * Show the settings page.
     * @return View
     */
    public function show(): View
    {
        return view(
            'settings',
            [
                'discordOauthURL' => $this->getDiscordOauthURL(),
                'user' => Auth::user(),
            ]
        );
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
        $userId = Auth::user()->id;

        $chatUser = ChatUser::where('server_id', $serverId)
            ->where('remote_user_id', $remoteUserId)
            ->where('user_id', $userId)
            ->where('server_type', ChatUser::TYPE_DISCORD)
            ->first();
        if (null !== $chatUser) {
            return redirect('settings')
                ->with('error', 'Discord user already registered.')
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

    public function linkIrc(LinkUserRequest $request): RedirectResponse
    {
        $serverId = $request->input('server-id');
        abort_if(
            !Str::contains($serverId, ':'),
            RedirectResponse::HTTP_BAD_REQUEST,
            'IRC servers should have both a hostname and a port, like chat.freenode.net:6667',
        );
        $remoteUserId = $request->input('user-id');

        // @phpstan-ignore-next-line
        $userId = $request->user()->id;

        $chatUser = ChatUser::irc()
            ->where('server_id', $serverId)
            ->where('remote_user_id', $remoteUserId)
            ->where('user_id', $userId)
            ->first();
        if (null !== $chatUser) {
            return redirect('settings')
                ->with('error', 'IRC user already registered.')
                ->withInput();
        }
        $chatUser = new ChatUser([
            'server_id' => $serverId,
            'server_name' => Str::limit(Str::before($serverId, ':'), 50),
            'server_type' => ChatUser::TYPE_IRC,
            'remote_user_id' => $remoteUserId,
            'remote_user_name' => $remoteUserId,
            'user_id' => $userId,
            'verified' => false,
        ]);
        $chatUser->save();

        return redirect('settings')
            ->with(
                'successObj',
                [
                    'id' => sprintf(
                        'success-irc-%s-%s',
                        $serverId,
                        $remoteUserId,
                    ),
                    'message' => sprintf(
                        'IRC account (%s - %s) linked.',
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
        $userId = Auth::user()->id;

        $chatUser = ChatUser::where('server_id', $serverId)
            ->where('remote_user_id', $remoteUserId)
            ->where('user_id', $userId)
            ->where('server_type', ChatUser::TYPE_SLACK)
            ->first();
        if (null !== $chatUser) {
            return redirect('settings')
                ->with('error', 'Slack user already registered.')
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
        if (str_starts_with((string)$request->input('server-id'), 'T')) {
            return $this->linkSlack($request);
        }
        if (is_numeric($request->input('server-id')[0])) {
            return $this->linkDiscord($request);
        }
        return $this->linkIrc($request);
    }
}
