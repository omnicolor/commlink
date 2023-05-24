<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Events\RollEvent;
use App\Exceptions\SlackException;
use App\Http\Requests\SlackRequest;
use App\Http\Responses\Slack\SlackResponse;
use App\Models\Channel;
use App\Models\ChatUser;
use App\Models\Slack\TextAttachment;
use App\Models\User;
use App\Rolls\Generic;
use App\Rolls\Roll;
use App\Traits\PrettifyRollsForSlack;
use Error;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\AbstractUser as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;

/**
 * Controller for handling Slack requests.
 */
class SlackController extends Controller
{
    use PrettifyRollsForSlack;

    /**
     * Arguments to the roll bot.
     * @var string[]
     */
    protected array $args;

    /**
     * Raw (unparsed) text of the command.
     * @var string
     */
    protected string $text;

    /**
     * Return a response for an OPTIONS request.
     * @return Response
     */
    public function options(): Response
    {
        return new Response('OK');
    }

    /**
     * Handle a user clicking a button attached to a response.
     * @param string $payload Payload Slack sent
     * @return ?SlackResponse
     */
    protected function handleAction(string $payload): ?SlackResponse
    {
        $request = json_decode($payload);

        $channel = $this->getChannel($request->team->id, $request->channel->id);
        $channel->user = $request->user->id;
        $character = $channel->character();

        if (null === $character) {
            Http::post(
                $request->response_url,
                [
                    'color' => TextAttachment::COLOR_DANGER,
                    'delete_original' => false,
                    'replace_original' => false,
                    'response_type' => 'ephemeral',
                    'text' => 'Only registered users can click action buttons',
                ]
            );
            return null;
        }

        if ($character->id !== $request->callback_id) {
            Http::post(
                $request->response_url,
                [
                    'color' => TextAttachment::COLOR_DANGER,
                    'delete_original' => false,
                    'replace_original' => false,
                    'response_type' => 'ephemeral',
                    'text' => 'Only the user that rolled can use this action',
                ]
            );
            return null;
        }

        $original_message = $request->original_message->attachments[0];
        $original_roll = $original_message->footer;
        $original_roll = str_replace(['*', '~'], ['', ''], $original_roll);
        /** @var array<int, int> */
        $original_roll = explode(' ', $original_roll);

        $successes = 0;
        $rerolled = 0;
        foreach ($original_roll as $key => $roll) {
            if (5 <= $roll) {
                $original_roll[$key] = (int)$roll;
                $successes++;
                continue;
            }
            $rerolled++;
            $roll = random_int(1, 6);
            if (5 <= $roll) {
                $successes++;
            }
            $original_roll[$key] = $roll;
        }

        \rsort($original_roll);
        return (new SlackResponse(channel: $channel))
            ->addAttachment(new TextAttachment(
                title: $original_message->title . ' with second chance',
                text: sprintf(
                    'Rolled %d successes (originally %s), rerolled %d',
                    $successes,
                    strtolower($original_message->text),
                    $rerolled
                ),
                color: TextAttachment::COLOR_SUCCESS,
                /**
                 * @psalm-suppress InvalidArgument
                 */
                footer: implode(' ', $this->prettifyRolls($original_roll)),
            ));
    }

    /**
     * Handle a POST from Slack.
     * @param SlackRequest $request
     * @return SlackResponse
     */
    public function post(SlackRequest $request): ?SlackResponse
    {
        if (isset($request->payload)) {
            return $this->handleAction($request->payload);
        }
        $this->args = \explode(' ', $request->text);
        $this->text = $request->text;

        $channel = $this->getChannel($request->team_id, $request->channel_id);
        $channel->user = $request->user_id;
        $character = $channel->character();
        if (null !== $character) {
            $channel->username = (string)$character;
        } else {
            $channel->username = $request->user_name ?? '';
        }

        // First, try to load system-specific rolls for numeric data.
        if (\is_numeric($this->args[0]) && isset($channel->system)) {
            try {
                $class = \sprintf(
                    '\\App\\Rolls\\%s\\Number',
                    \ucfirst($channel->system)
                );
                /** @var Roll */
                $roll = new $class($this->text, $channel->username, $channel);
                RollEvent::dispatch($roll, $channel);
                return $roll->forSlack();
            } catch (Error $ex) {
                // Ignore errors here, they might want a generic command.
                Log::debug($ex->getMessage());
            }
        }

        // Next, try system-specific rolls that aren't numeric.
        if (null !== $channel->system) {
            try {
                $class = \sprintf(
                    '\\App\\Rolls\\%s\\%s',
                    \str_replace(' ', '', \ucwords(\str_replace('-', ' ', $channel->system ?? 'Unknown'))),
                    \ucfirst($this->args[0])
                );
                /** @var Roll */
                $roll = new $class($this->text, $channel->username, $channel);
                if ('help' !== $this->args[0]) {
                    RollEvent::dispatch($roll, $channel);
                }
                return $roll->forSlack();
            } catch (Error $ex) {
                // Again, ignore errors, they might want a generic command.
                Log::debug($ex->getMessage());
            }
        }

        // No system-specific response found, see if the request is a generic
        // XdY roll.
        if (1 === \preg_match('/\d+d\d+/i', $this->args[0])) {
            $roll = new Generic($this->text, $channel->username, $channel);
            RollEvent::dispatch($roll, $channel);
            return $roll->forSlack();
        }

        // See if there's a Roll that isn't system-specific.
        try {
            $class = \sprintf('\\App\\Rolls\\%s', \ucfirst($this->args[0]));
            /** @var Roll */
            $roll = new $class($this->text, $channel->username, $channel);
            if ('help' !== $this->args[0]) {
                RollEvent::dispatch($roll, $channel);
            }
            return $roll->forSlack();
        } catch (Error $ex) {
            // Again, ignore errors, they might want an old-school response.
            Log::debug($ex->getMessage());
        }

        // Finally, see if there's a Slack response that isn't system-specific.
        try {
            $class = \sprintf(
                '\\App\Http\\Responses\\Slack\\%sResponse',
                \ucfirst($this->args[0])
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
                . \PHP_EOL . \PHP_EOL . 'Type `/roll help` for more help.'
            );
        }
    }

    /**
     * Get the channel attached to the request.
     * @param string $team Slack team ID (server)
     * @param string $channel Slack channel ID
     * @return Channel
     */
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
     * @psalm-suppress InvalidReturnStatement
     * @psalm-suppress InvalidReturnType
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
