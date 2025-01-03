<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ChatUser;
use App\Models\Traits\InteractsWithDiscord;
use App\Models\User;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\One\User as SocialiteUser;
use Laravel\Socialite\Two\InvalidStateException;
use RuntimeException;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;

use function assert;
use function collect;
use function count;
use function redirect;
use function session;
use function sprintf;
use function view;

/**
 * @phpstan-type Guild array{icon: ?string, name: string, snowflake: string}
 */
class DiscordController extends Controller
{
    use InteractsWithDiscord;

    protected const DISCORD_CODE_LENGTH = 30;

    /**
     * Handles a POST from the Discord view.
     *
     * Should receive a list of Guild IDs that the user wants to register.
     */
    public function save(Request $request): RedirectResponse
    {
        /** @var User */
        $commlinkUser = Auth::user();

        /** @var array<int, Guild> */
        $potentialGuilds = $request->session()->pull('guilds');
        /** @var Collection<string, Guild> */
        $potentialGuilds = collect($potentialGuilds)->keyBy('snowflake');
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
            assert(null !== $potentialGuilds[$guild]);

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
     */
    public function handleCallback(): RedirectResponse
    {
        try {
            /** @var SocialiteUser */
            $socialUser = Socialite::driver('discord')->user();
        } catch (InvalidStateException $ex) {
            Log::error('Invalid state exception: ' . $ex->getMessage());
            return redirect()->route('settings')
                ->withErrors(['error' => $ex->getMessage()]);
        } catch (ClientException $ex) {
            Log::error('Discord client not set up: ' . $ex->getMessage());
            return redirect()->route('settings')
                ->withErrors(['error' => 'Discord login failed on our side, nothing you did wrong!']);
        }
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
        } elseif (self::DISCORD_CODE_LENGTH !== strlen((string)$request->input('code'))) {
            return redirect()->route('settings')->withErrors([
                'error' => 'Discord login failed, invalid Oauth code',
            ]);
        } else {
            try {
                $accessToken = $this->getDiscordAccessToken($request->input('code'));
                $discordUser = $this->getDiscordUser($accessToken);
            } catch (RuntimeException $ex) {
                Log::error($ex->getMessage());
                return redirect()
                    ->route('settings')
                    ->withErrors([
                        'error' => sprintf(
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
        } catch (RuntimeException $ex) {
            Log::error($ex->getMessage());
            return redirect()
                ->route('settings')
                ->withErrors([
                    'error' => sprintf(
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
                if (!$registeredGuilds->has((string)$guild['snowflake'])) {
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
