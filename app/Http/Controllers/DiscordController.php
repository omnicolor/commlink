<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ChatUser;
use App\Models\Traits\InteractsWithDiscord;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DiscordController extends Controller
{
    use InteractsWithDiscord;

    /**
     * Handles a POST from the Discord view.
     *
     * Should receive a list of Guild IDs that the user wants to register.
     * @param Request $request
     * @return RedirectResponse
     */
    public function save(Request $request): RedirectResponse
    {
        /** @var User */
        $commlinkUser = \Auth::user();

        $potentialGuilds = collect($request->session()->pull('guilds'))
            ->keyBy('snowflake');
        $guilds = $request->input('guilds');
        if (null === $guilds || !is_array($guilds)) {
            return redirect()->route('settings')
                ->withErrors(['error' => 'No guilds selected.']);
        }

        $discordUser = $request->session()->pull('discordUser');
        foreach ($guilds as $guild) {
            if (!$potentialGuilds->has($guild)) {
                return redirect()
                    ->route('settings')
                    ->withErrors(['error' => 'An invalid Guild ID was found.']);
            }

            ChatUser::create([
                'server_id' => $guild,
                'server_name' => $potentialGuilds[$guild]['name'],
                'server_type' => ChatUser::TYPE_DISCORD,
                'remote_user_id' => $discordUser['snowflake'],
                'remote_user_name' => $discordUser['username'] . '#'
                    . $discordUser['discriminator'],
                'user_id' => $commlinkUser->id,
                'verified' => false,
            ]);
        }

        $count = count($guilds);
        return redirect()->route('settings')->with(
            'success',
            sprintf(
                '%d Discord %s linked!',
                \Str::plural('user', $count),
                $count,
            )
        );
    }

    /**
     * View the Discord linking page.
     *
     * Handles a redirect from the Oauth2 login page from Discord, then uses the
     * code to make requests back to Discord's API as the Discord user to get
     * information about them as well as the list of Guilds they're in. We then
     * present that to the user to allow them to automatically link a number of
     * Guilds for that Discord user.
     * @param Request $request
     * @return RedirectResponse | View
     */
    public function view(Request $request): RedirectResponse | View
    {
        if (null === $request->input('code')) {
            return redirect()->route('settings')->withErrors([
                'error' => 'Discord login failed, no Oauth code supplied',
            ]);
        }

        if (30 !== strlen($request->input('code'))) {
            return redirect()->route('settings')->withErrors([
                'error' => 'Discord login failed, invalid Oauth code',
            ]);
        }

        try {
            $accessToken = $this->getDiscordAccessToken($request->input('code'));
            $discordUser = $this->getDiscordUser($accessToken);
            $guilds = $this->getDiscordGuilds($accessToken);
            session([
                'guilds' => $guilds,
                'discordUser' => $discordUser,
            ]);
        } catch (\RuntimeException) {
            return redirect()
                ->route('settings')
                ->withErrors([
                    'error' => \sprintf(
                        'Request to Discord failed. Please <a href="%s">try again</a>.',
                        $this->getDiscordOauthURL(),
                    ),
                ]);
        }

        /** @var User */
        $commlinkUser = \Auth::user();
        $registeredGuilds = ChatUser::discord()
            ->where('user_id', $commlinkUser->id)
            ->where('remote_user_id', $discordUser['snowflake'])
            ->pluck('server_id')
            ->flip();

        return view(
            'discord-choose-guild',
            [
                'discordUser' => $discordUser,
                'guilds' => $guilds,
                'registeredGuilds' => $registeredGuilds,
                'user' => $commlinkUser,
            ]
        );
    }
}
