<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Events\RollEvent;
use App\Exceptions\SlackException;
use App\Http\Requests\SlackRequest;
use App\Http\Responses\Slack\SlackResponse;
use App\Models\Channel;
use App\Models\ChatUser;
use App\Models\User;
use App\Rolls\Generic;
use App\Rolls\Roll;
use Error;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\AbstractUser as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;
use Nwidart\Modules\Facades\Module;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;

use function explode;
use function is_numeric;
use function preg_match;
use function sprintf;
use function ucfirst;

use const PHP_EOL;

/**
 * Controller for handling Slack requests.
 * @psalm-suppress UnusedClass
 */
class SlackController extends Controller
{
    /**
     * Arguments to the roll bot.
     * @var array<int, string>
     */
    protected array $args;

    /**
     * Raw (unparsed) text of the command.
     */
    protected string $text;

    public function options(): Response
    {
        return new Response('OK');
    }

    public function post(SlackRequest $request): ?SlackResponse
    {
        if (isset($request->payload)) {
            return $this->handleAction($request->payload);
        }

        $this->args = explode(' ', $request->text);
        $this->text = $request->text;

        $channel = $this->getChannel($request->team_id, $request->channel_id);
        $channel->user = $request->user_id;
        $character = $channel->character();
        if (null !== $character) {
            $channel->username = (string)$character;
        } else {
            $channel->username = $request->user_name ?? '';
        }

        // First, try to load a system-specific roll for a module.
        if (isset($channel->system) && null !== Module::find($channel->system)) {
            if (is_numeric($this->args[0])) {
                $class = sprintf(
                    '\\Modules\\%s\\Rolls\\Number',
                    ucfirst($channel->system),
                );
                try {
                    /** @var Roll */
                    $roll = new $class($this->text, $channel->username, $channel);
                    RollEvent::dispatch($roll, $channel);
                    return $roll->forSlack();
                } catch (Error) { // @codeCoverageIgnore
                    // Ignore errors here, they might want a generic command.
                }
            }
            // Next, module rolls that aren't numeric.
            $class = sprintf(
                '\\Modules\\%s\\Rolls\\%s',
                ucfirst($channel->system),
                ucfirst($this->args[0]),
            );
            try {
                /** @var Roll */
                $roll = new $class($this->text, $channel->username, $channel);
                if ('help' !== $this->args[0]) {
                    RollEvent::dispatch($roll, $channel);
                }
                return $roll->forSlack();
            } catch (Error $ex) {
                // Again, ignore errors, they might want a generic command.
            }
        }

        // No system-specific response found, see if the request is a generic
        // XdY roll.
        if (1 === preg_match('/\d+d\d+/i', $this->args[0])) {
            $roll = new Generic($this->text, $channel->username, $channel);
            RollEvent::dispatch($roll, $channel);
            return $roll->forSlack();
        }

        // See if there's a Roll that isn't system-specific.
        try {
            $class = sprintf('\\App\\Rolls\\%s', ucfirst($this->args[0]));
            /** @var Roll */
            $roll = new $class($this->text, $channel->username, $channel);
            if ('help' !== $this->args[0]) {
                RollEvent::dispatch($roll, $channel);
            }
            return $roll->forSlack();
        } catch (Error) {
            // Again, ignore errors, they might want an old-school response.
        }

        // Finally, see if there's a Slack response that isn't system-specific.
        try {
            $class = sprintf(
                '\\App\Http\\Responses\\Slack\\%sResponse',
                ucfirst($this->args[0])
            );
            /** @var SlackResponse */
            $response = new $class(content: $this->text, channel: $channel);
            return $response;
        } catch (Error $ex) {
            Log::debug(
                '{system} - Could not find roll "{roll}" from user "{user}"',
                [
                    'system' => $channel->system,
                    'roll' => $this->text,
                    'user' => $channel->username,
                    'exception' => $ex->getMessage(),
                ],
            );
            throw new SlackException(
                'That doesn\'t appear to be a valid Commlink command.'
                . PHP_EOL . PHP_EOL . 'Type `/roll help` for more help.'
            );
        }
    }

    protected function handleAction(string $payload): ?SlackResponse
    {
        $request = json_decode($payload);
        if (
            null === $request
            || !property_exists($request, 'team')
            || !property_exists($request, 'channel')
            || !property_exists($request, 'user')
            || !is_object($request->user)
            || !property_exists($request->user, 'id')
            || !property_exists($request->user, 'name')
            || !property_exists($request, 'actions')
            || !array_key_exists(0, $request->actions)
            || !is_object($request->actions[0])
            || !property_exists($request->actions[0], 'action_id')
        ) {
            throw new SlackException('Invalid action payload');
        }
        $channel = $this->getChannel($request->team->id, $request->channel->id);
        $channel->user = $request->user->id;

        $action = explode(':', $request->actions[0]->action_id);
        $action = '\\App\Rolls\\' . ucfirst($action[0]);

        try {
            /** @var Roll */
            $roll = new $action($payload, $request->user->name, $channel);
        } catch (Error) {
            throw new SlackException('Invalid action callback');
        }
        $roll->handleSlackAction();

        return null;
    }

    protected function getChannel(string $team, string $channel): Channel
    {
        try {
            return Channel::slack()
                ->where('channel_id', $channel)
                ->where('server_id', $team)
                ->firstOrFail();
        } catch (ModelNotFoundException) {
            return new Channel([
                'channel_id' => $channel,
                'server_id' => $team,
                'type' => Channel::TYPE_SLACK,
            ]);
        }
    }

    /**
     * Handle a successful login from Slack.
     */
    public function handleCallback(): RedirectResponse
    {
        /** @var SocialiteUser */
        $socialUser = Socialite::driver('slack')->user();

        $user = User::where('email', $socialUser->email)->first();
        if (null === $user) {
            // The user wasn't found, create a new one.
            $user = User::create([
                'email' => $socialUser->email,
                'name' => $socialUser->name,
                'password' => 'reset me',
            ]);
        }

        $chatUser = ChatUser::slack()
            ->where('server_id', $socialUser->attributes['organization_id'])
            ->where('remote_user_id', $socialUser->id)
            ->where('user_id', $user->id)
            ->first();
        if (null === $chatUser) {
            ChatUser::create([
                'server_id' => $socialUser->attributes['organization_id'],
                'server_name' => $socialUser->user['team']['name'],
                'server_type' => ChatUser::TYPE_SLACK,
                'remote_user_id' => $socialUser->id,
                'remote_user_name' => $socialUser->name,
                'user_id' => $user->id,
            ]);
        }

        Auth::login($user);
        return redirect('/dashboard');
    }

    /**
     * The user wants to login to Commlink using their Slack login.
     */
    public function redirectToSlack(): SymfonyRedirectResponse
    {
        return Socialite::driver('slack')->redirect();
    }
}
