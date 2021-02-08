<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\SlackRequest;
use App\Http\Responses\SlackResponse;
use App\Models\Slack\Channel;
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
        $this->args = explode(' ', $request->text);
        $this->text = $request->text;

        $channel = $this->getChannel($request->team_id, $request->channel_id);
        $channel->user = $request->user_id;
        $channel->username = $request->user_name ?? '';

        // First, try to load system-specific responses.
        try {
            $class = sprintf(
                '\\App\Http\\Responses\\%s\\%sResponse',
                ucfirst($channel->system),
                ucfirst($this->args[0])
            );
            return new $class($this->text, 200, [], $channel);
        } catch (\Error $ex) {
            // Ignore errors here, they might want a generic command.
        }

        // No system-specific response found, try generic ones.
        try {
            $class = sprintf(
                '\\App\Http\\Responses\\%sResponse',
                ucfirst($this->args[0])
            );
            return new $class($this->text, 200, [], $channel);
        } catch (\Error $ex) {
            throw new \App\Exceptions\SlackException(
                'That doesn\'t appear to be a valid Commlink command.' . PHP_EOL
                . PHP_EOL . 'Type `/roll help` for more help.'
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
            return Channel::where('channel', $channel)
                ->where('team', $team)
                ->firstOrFail();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            return new Channel(['channel' => $channel, 'team' => $team]);
        }
    }
}
