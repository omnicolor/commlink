<?php

declare(strict_types=1);

namespace App\Http\Responses;

use App\Exceptions\SlackException;
use App\Models\Slack\Channel;
use App\Models\Slack\TextAttachment;

/**
 * Slack response for registering a channel for a particular system.
 */
class RegisterResponse extends SlackResponse
{
    /**
     * Constructor.
     * @param string $content
     * @param int $status
     * @param array<string, string> $headers
     * @param Channel $channel
     * @throws SlackException
     */
    public function __construct(
        string $content = '',
        int $status = 200,
        array $headers = [],
        Channel $channel = null
    ) {
        parent::__construct('', $status, $headers, $channel);
        if (is_null($channel)) {
            throw new SlackException(('Channel doesn\'t exist'));
        }
        if ('unregistered' !== $channel->system) {
            throw new SlackException(sprintf(
                'This channel is already registered for "%s"',
                $channel->system
            ));
        }
        $systems = config('app.systems');
        $args = explode(' ', $content);
        if (2 !== count($args)) {
            throw new SlackException(sprintf(
                'To register a channel, use `register [system]`, where system '
                    . 'is a system code: %s',
                implode(', ', array_keys($systems))
            ));
        }
        if (!array_key_exists($args[1], $systems)) {
            throw new SlackException(sprintf(
                '"%s" is not a valid system code. Use `register [system]`, '
                . 'where system is: %s',
                $args[1],
                implode(', ', array_keys($systems))
            ));
        }
        $channel->system = $args[1];
        $channel->save();
        $this->addAttachment(new TextAttachment(
            'Registered',
            sprintf(
                '%s has registered this channel for the "%s" system.',
                $channel->username,
                $systems[$args[1]]
            ),
            TextAttachment::COLOR_SUCCESS
        ))
            ->sendToChannel();
    }
}
