<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ChatUser;
use App\Models\Traits\InteractsWithDiscord;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Laravel\Socialite\AbstractUser as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;
use RuntimeException;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;

class DiscordController extends Controller
{
    use InteractsWithDiscord;

    /**
     * Handles a POST from the Discord view.
     *
     * Should receive a list of Guild IDs that the user wants to register.
     */
    public function save(Request $request): RedirectResponse
    {
        /** @var User */
        $commlinkUser = Auth::user();

        // @phpstan-ignore-next-line
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
                $count,
                Str::plural('user', $count),
            )
        );
    }

    /**
     * Handle a successful login from Discord.
     * @psalm-suppress InvalidReturnType
     * @psalm-suppress InvalidReturnStatement
     */
    public function handleCallback(): RedirectResponse
    {
        /** @var SocialiteUser */
        $socialUser = Socialite::driver('discord')->user();
        $user = User::where('email', $socialUser->email)->first();

        if (null === $user) {
            // The user wasn't found, create a new one.
            $user = User::create([
                'email' => $socialUser->email,
                'name' => $socialUser->name,
                'password' => 'reset me',
            ]);
        }

        Auth::login($user);
        session(['discordUser' => [
            // @phpstan-ignore-next-line
            'token' => $socialUser->token,
            'avatar' => $socialUser->avatar,
            'snowflake' => $socialUser->id,
            'username' => $socialUser->name,
            'discriminator' => $socialUser->user['discriminator'],
        ]]);
        return redirect('/discord');
    }

    /**
     * The user wants to login to Commlink using their Discord login.
     */
    public function redirectToDiscord(): SymfonyRedirectResponse
    {
        return Socialite::driver('discord')->redirect();
    }

    /**
     * View the Discord linking page.
     *
     * Handles a redirect from the Oauth2 login page from Discord, then uses the
     * code to make requests back to Discord's API as the Discord user to get
     * information about them as well as the list of Guilds they're in. We then
     * present that to the user to allow them to automatically link a number of
     * Guilds for that Discord user.
     */
    public function view(Request $request): RedirectResponse | View
    {
        $wasDiscordLogin = false;
        if ($request->session()->has('discordUser')) {
            $discordUser = $request->session()->pull('discordUser');
            $accessToken = $discordUser['token'];
            $wasDiscordLogin = true;
        } elseif (null === $request->input('code')) {
            return redirect()->route('settings')->withErrors([
                'error' => 'Discord login failed, no Oauth code supplied',
            ]);
        } elseif (30 !== strlen($request->input('code'))) {
            return redirect()->route('settings')->withErrors([
                'error' => 'Discord login failed, invalid Oauth code',
            ]);
        } else {
            try {
                $accessToken = $this->getDiscordAccessToken($request->input('code'));
                $discordUser = $this->getDiscordUser($accessToken);
            } catch (RuntimeException) {
                return redirect()
                    ->route('settings')
                    ->withErrors([
                        'error' => \sprintf(
                            'Request to Discord failed. Please <a href="%s">try again</a>.',
                            $this->getDiscordOauthURL(),
                        ),
                    ]);
            }
        }

        try {
            $guilds = $this->getDiscordGuilds($accessToken);
            session([
                'guilds' => $guilds,
                'discordUser' => $discordUser,
            ]);
        } catch (RuntimeException) {
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
        $commlinkUser = Auth::user();
        $registeredGuilds = ChatUser::discord()
            ->where('user_id', $commlinkUser->id)
            ->where('remote_user_id', $discordUser['snowflake'])
            ->pluck('server_id')
            ->flip();

        if ($wasDiscordLogin) {
            $allGuildsRegistered = true;
            // Check to see if all guilds have already been registered.
            foreach ($guilds as $guild) {
                // @phpstan-ignore-next-line
                if (!$registeredGuilds->has($guild['snowflake'])) {
                    $allGuildsRegistered = false;
                    break;
                }
            }

            // If the user has already registered all of their guilds, don't
            // show the guild registration page, just go to the dashboard.
            if ($allGuildsRegistered) {
                return redirect()->route('dashboard');
            }
        }

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
