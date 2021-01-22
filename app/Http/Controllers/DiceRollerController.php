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
        throw new \App\Exceptions\SlackException(
            'That doesn\'t appear to be a valid Commlink command.' . PHP_EOL
                . PHP_EOL . 'Type `/roll help` for more help.'
        );
        //return (new SlackResponse('', Response::HTTP_OK))->setText('Okay');
    }
}
