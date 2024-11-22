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

use function abort_if;
use function is_numeric;
use function redirect;
use function sprintf;
use function str_starts_with;
use function view;

/**
 * @psalm-suppress UnusedClass
 */
class SettingsController extends Controller
{
    use InteractsWithDiscord;

    /**
     * Show the settings page.
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
     */
    public function linkDiscord(LinkUserRequest $request): RedirectResponse
    {
        $server_id = $request->input('server-id');
        $remote_user_id = $request->input('user-id');

        /** @var int User must be logged in to make the request */
        $user_id = $request->user()?->id;

        $chat_user = ChatUser::where('server_id', $server_id)
            ->where('remote_user_id', $remote_user_id)
            ->where('user_id', $user_id)
            ->where('server_type', ChatUser::TYPE_DISCORD)
            ->first();
        if (null !== $chat_user) {
            return redirect('settings')
                ->with('error', 'Discord user already registered.')
                ->withInput();
        }

        $chat_user = new ChatUser([
            'server_id' => $server_id,
            'server_type' => ChatUser::TYPE_DISCORD,
            'remote_user_id' => $remote_user_id,
            'user_id' => $user_id,
            'verified' => false,
        ]);
        $chat_user->server_name = $chat_user->getDiscordServerName($server_id);
        $chat_user->remote_user_name
            = $chat_user->getDiscordUserName($remote_user_id);
        $chat_user->save();

        return redirect('settings')
            ->with(
                'successObj',
                [
                    'id' => sprintf(
                        'success-discord-%s-%s',
                        $server_id,
                        $remote_user_id,
                    ),
                    'message' => sprintf(
                        'Discord account (%s - %s) linked.',
                        $chat_user->server_name,
                        $chat_user->remote_user_name,
                    ),
                ],
            );
    }

    public function linkIrc(LinkUserRequest $request): RedirectResponse
    {
        $server_id = $request->input('server-id');
        abort_if(
            !Str::contains($server_id, ':'),
            RedirectResponse::HTTP_BAD_REQUEST,
            'IRC servers should have both a hostname and a port, like chat.freenode.net:6667',
        );
        $remote_user_id = $request->input('user-id');

        /** @var int User must be logged in to make the request */
        $user_id = $request->user()?->id;

        $chat_user = ChatUser::irc()
            ->where('server_id', $server_id)
            ->where('remote_user_id', $remote_user_id)
            ->where('user_id', $user_id)
            ->first();
        if (null !== $chat_user) {
            return redirect('settings')
                ->with('error', 'IRC user already registered.')
                ->withInput();
        }
        $chat_user = new ChatUser([
            'server_id' => $server_id,
            'server_name' => Str::limit(Str::before($server_id, ':'), 50),
            'server_type' => ChatUser::TYPE_IRC,
            'remote_user_id' => $remote_user_id,
            'remote_user_name' => $remote_user_id,
            'user_id' => $user_id,
            'verified' => false,
        ]);
        $chat_user->save();

        return redirect('settings')
            ->with(
                'successObj',
                [
                    'id' => sprintf(
                        'success-irc-%s-%s',
                        $server_id,
                        $remote_user_id,
                    ),
                    'message' => sprintf(
                        'IRC account (%s - %s) linked.',
                        $chat_user->server_name,
                        $chat_user->remote_user_name,
                    ),
                ],
            );
    }

    /**
     * Handle a request to link a Slack team/user to the current Commlink user.
     */
    protected function linkSlack(LinkUserRequest $request): RedirectResponse
    {
        $server_id = $request->input('server-id');
        $remote_user_id = $request->input('user-id');
        /** @var int User must be logged in to make the request */
        $user_id = $request->user()?->id;

        $chat_user = ChatUser::where('server_id', $server_id)
            ->where('remote_user_id', $remote_user_id)
            ->where('user_id', $user_id)
            ->where('server_type', ChatUser::TYPE_SLACK)
            ->first();
        if (null !== $chat_user) {
            return redirect('settings')
                ->with('error', 'Slack user already registered.')
                ->withInput();
        }

        $chat_user = new ChatUser([
            'server_id' => $server_id,
            'server_type' => ChatUser::TYPE_SLACK,
            'remote_user_id' => $remote_user_id,
            'user_id' => $user_id,
            'verified' => false,
        ]);
        $chat_user->server_name = $chat_user->getSlackTeamName($server_id);
        $chat_user->remote_user_name
            = $chat_user->getSlackUserName($remote_user_id);
        $chat_user->save();

        return redirect('settings')
            ->with(
                'successObj',
                [
                    'id' => sprintf(
                        'success-slack-%s-%s',
                        $server_id,
                        $remote_user_id,
                    ),
                    'message' => sprintf(
                        'Slack account (%s - %s) linked.',
                        $chat_user->server_name,
                        $chat_user->remote_user_name,
                    ),
                ],
            );
    }

    /**
     * Handle a request to link a chat server to the current Commlink user.
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
