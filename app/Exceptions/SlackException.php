<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Http\Responses\SlackResponse;
use App\Models\Slack\TextAttachment;
use Exception;

/**
 * Exception thrown when a Slack command doesn't have the required fields.
 */
class SlackException extends Exception
{
    /**
     * HTTP status code to return with the response.
     * @var int
     */
    protected $code = 200;

    /**
     * Render the exception as a Slack Response to return to the client.
     * @return SlackResponse
     */
    public function render(): SlackResponse
    {
        if ('' === $this->message) {
            $this->message = 'You must include at least one command '
            . 'argument.' . PHP_EOL
            . 'For example: `/roll init` to roll your character\'s '
            . 'initiative.' . PHP_EOL . PHP_EOL
            . 'Type `/roll help` for more help.';
        }
        return (new SlackResponse('', $this->code))
            ->addAttachment(new TextAttachment(
                'Error',
                $this->message,
                TextAttachment::COLOR_DANGER
            ));
    }
}
