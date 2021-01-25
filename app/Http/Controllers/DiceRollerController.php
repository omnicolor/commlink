<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\SlackRequest;
use App\Http\Responses\SlackResponse;
use Illuminate\Http\Response;

/**
 * Controller for handling Slack requests.
 */
class DiceRollerController extends Controller
{
    /**
     * Arguments to the roll bot.
     * @var string[]
     */
    protected array $args;

    /**
     * Slack channel ID the command came from.
     * @var string
     */
    protected string $channelId;

    /**
     * Slack team ID (server) the command came from.
     * @var string
     */
    protected string $teamId;

    /**
     * Raw (unparsed) text of the command.
     * @var string
     */
    protected string $text;

    /**
     * Slack user ID that entered the command.
     * @var string
     */
    protected string $userId;

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
        $this->channelId = $request->channel_id;
        $this->teamId = $request->team_id;
        $this->text = $request->text;
        $this->userId = $request->user_id;

        try {
            $class = sprintf(
                '\\App\Http\\Responses\\%sResponse',
                ucfirst($this->text)
            );
            \Log::info('Trying to load ' . $class);
            return new $class();
        } catch (\Error $ex) {
            throw new \App\Exceptions\SlackException(
                'That doesn\'t appear to be a valid Commlink command.' . PHP_EOL
                . PHP_EOL . 'Type `/roll help` for more help.'
            );
        }
    }
}
