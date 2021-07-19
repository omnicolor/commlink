<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Events\RollEvent;
use App\Exceptions\SlackException;
use App\Http\Requests\SlackRequest;
use App\Http\Responses\SlackResponse;
use App\Models\Channel;
use App\Rolls\Generic;
use Error;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;

/**
 * Controller for handling Slack requests.
 */
class SlackController extends Controller
{
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
        return response('OK');
    }

    /**
     * Handle a POST from Slack.
     * @param SlackRequest $request
     * @return SlackResponse
     */
    public function post(SlackRequest $request): SlackResponse
    {
        $this->args = \explode(' ', $request->text);
        $this->text = $request->text;

        $channel = $this->getChannel($request->team_id, $request->channel_id);
        $channel->user = $request->user_id;
        $channel->username = $request->user_name ?? '';

        // First, try to load system-specific rolls for numeric data.
        if (\is_numeric($this->args[0]) && isset($channel->system)) {
            try {
                $class = \sprintf(
                    '\\App\\Rolls\\%s\\Number',
                    \ucfirst($channel->system)
                );
                $roll = new $class($this->text, $channel->username);
                RollEvent::dispatch($roll, $channel);
                return $roll->forSlack($channel);
            } catch (Error) {
                // Ignore errors here, they might want a generic command.
            }
        }

        // Next, try system-specific rolls that aren't numeric.
        try {
            $class = \sprintf(
                '\\App\\Rolls\\%s\\%s',
                \ucfirst($channel->system ?? 'Unknown'),
                \ucfirst($this->args[0])
            );
            return new $class($this->text, 200, [], $channel);
        } catch (Error) {
            // Again, ignore errors, they might want a generic command.
        }

        // Now try Slack-specific responses.
        try {
            $class = \sprintf(
                '\\App\Http\\Responses\\%s\\%sResponse',
                \ucfirst($channel->system ?? 'Unknown'),
                \ucfirst($this->args[0])
            );
            return new $class($this->text, 200, [], $channel);
        } catch (Error) {
            // Again, ignore errors, they might want a generic command.
        }

        // No system-specific response found, see if the request is a generic
        // XdY roll.
        if (1 === \preg_match('/\d+d\d+/i', $this->args[0])) {
            $roll = new Generic($this->text, $channel->username);
            RollEvent::dispatch($roll, $channel);
            return $roll->forSlack($channel);
        }

        // Finally, see if there's a Slack response that isn't system-specific.
        try {
            $class = \sprintf(
                '\\App\Http\\Responses\\%sResponse',
                \ucfirst($this->args[0])
            );
            return new $class(
                $this->text,
                SlackResponse::HTTP_OK,
                [],
                $channel
            );
        } catch (Error) {
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
}
