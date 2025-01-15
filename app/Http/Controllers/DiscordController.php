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
        $commlink_user = Auth::user();

        /** @var array<int, Guild> */
        $potential_guilds = $request->session()->pull('guilds');
        /** @var Collection<string, Guild> */
        $potential_guilds = collect($potential_guilds)->keyBy('snowflake');
        $guilds = $request->input('guilds');
        if (null === $guilds || !is_array($guilds)) {
            return redirect()->route('settings')
                ->withErrors(['error' => 'No guilds selected.']);
        }

        $discord_user = $request->session()->pull('discordUser');
        foreach ($guilds as $guild) {
            if (!$potential_guilds->has($guild)) {
                return redirect()
                    ->route('settings')
                    ->withErrors(['error' => 'An invalid Guild ID was found.']);
            }
            assert(null !== $potential_guilds[$guild]);

            ChatUser::create([
                'server_id' => $guild,
                'server_name' => $potential_guilds[$guild]['name'],
                'server_type' => ChatUser::TYPE_DISCORD,
                'remote_user_id' => $discord_user['snowflake'],
                'remote_user_name' => $discord_user['username'] . '#'
                    . $discord_user['discriminator'],
                'user_id' => $commlink_user->id,
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
            $social_user = Socialite::driver('discord')->user();
        } catch (InvalidStateException $ex) {
            Log::error('Invalid state exception: ' . $ex->getMessage());
            return redirect()->route('settings')
                ->withErrors(['error' => $ex->getMessage()]);
        } catch (ClientException $ex) {
            Log::error('Discord client not set up: ' . $ex->getMessage());
            return redirect()->route('settings')
                ->withErrors(['error' => 'Discord login failed on our side, nothing you did wrong!']);
        }
        $user = User::where('email', $social_user->email)->first();

        if (null === $user) {
            // The user wasn't found, create a new one.
            $user = User::create([
                'email' => $social_user->email,
                'name' => $social_user->name,
                'password' => 'reset me',
            ]);
        }

        Auth::login($user);
        session(['discordUser' => [
            'token' => $social_user->token,
            'avatar' => $social_user->avatar,
            'snowflake' => $social_user->id,
            'username' => $social_user->name,
            'discriminator' => $social_user->user['discriminator'],
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
        $was_discord_login = false;
        if ($request->session()->has('discordUser')) {
            $discord_user = $request->session()->pull('discordUser');
            $access_token = $discord_user['token'];
            $was_discord_login = true;
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
                $access_token = $this->getDiscordAccessToken($request->input('code'));
                $discord_user = $this->getDiscordUser($access_token);
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
            $guilds = $this->getDiscordGuilds($access_token);
            session([
                'guilds' => $guilds,
                'discordUser' => $discord_user,
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
        $commlink_user = Auth::user();
        $registered_guilds = ChatUser::discord()
            ->where('user_id', $commlink_user->id)
            ->where('remote_user_id', $discord_user['snowflake'])
            ->pluck('server_id')
            ->flip();

        if ($was_discord_login) {
            $all_guilds_registered = true;
            // Check to see if all guilds have already been registered.
            foreach ($guilds as $guild) {
                if (!$registered_guilds->has((string)$guild['snowflake'])) {
                    $all_guilds_registered = false;
                    break;
                }
            }

            // If the user has already registered all of their guilds, don't
            // show the guild registration page, just go to the dashboard.
            if ($all_guilds_registered) {
                return redirect()->route('dashboard');
            }
        }

        return view(
            'discord-choose-guild',
            [
                'discordUser' => $discord_user,
                'guilds' => $guilds,
                'registeredGuilds' => $registered_guilds,
                'user' => $commlink_user,
            ]
        );
    }
}
