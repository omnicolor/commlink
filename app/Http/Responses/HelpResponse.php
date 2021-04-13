<?php

declare(strict_types=1);

namespace App\Http\Responses;

use App\Exceptions\SlackException;
use App\Models\Channel;
use App\Models\Slack\TextAttachment;

/**
 * Generic help response for Slack.
 */
class HelpResponse extends SlackResponse
{
    /**
     * Constructor.
     * @param string $content
     * @param int $status
     * @param array<string, string> $headers
     * @param ?Channel $channel
     */
    public function __construct(
        string $content = '',
        int $status = 200,
        array $headers = [],
        ?Channel $channel = null
    ) {
        parent::__construct($content, $status, $headers, $channel);
        if (is_null($channel)) {
            throw new SlackException('Channel is required');
        }

        $this->addAttachment(new TextAttachment(
            sprintf('About %s', config('app.name')),
            sprintf(
                '%1$s is a Slack bot that lets you roll dice appropriate for '
                    . 'various RPG systems. For example, if you are playing '
                    . 'The Expanse, it will roll three dice, marking one of '
                    . 'them as the "drama die", adding up the result with the '
                    . 'number you give for your attribute+focus score, and '
                    . 'return the result along with any stunt points.' . PHP_EOL
                    . PHP_EOL . 'If your game uses the web app for '
                    . '<%2$s|%1$s> as well, links in the app will '
                    . 'automatically roll in Slack, and changes made to your '
                    . 'character via Slack will appear in %1$s.',
                config('app.name'),
                config('app.url')
            ),
            TextAttachment::COLOR_INFO
        ));

        $systems = [];
        foreach (config('app.systems') as $code => $name) {
            $systems[] = sprintf('· %s (%s)', $name, $code);
        }
        $this->addAttachment(new TextAttachment(
            'Supported Systems',
            'The current channel is not registered for any of the systems.'
                . PHP_EOL . implode(PHP_EOL, $systems),
            TextAttachment::COLOR_INFO
        ));

        $chatUser = $this->channel->getChatUser();
        if (is_null($chatUser)) {
            $this->addAttachment(new TextAttachment(
                'Note for unregistered users:',
                sprintf(
                    'Your Slack user has not been linked with a %s user. '
                        . 'Go to the <%s/settings|settings page> and copy the '
                        . 'command listed there for this server. If the server '
                        . 'isn\'t listed, follow the instructions there to add '
                        . 'it. You\'ll need to know your server ID (`%s`) and '
                        . 'your user ID (`%s`).',
                    config('app.name'),
                    config('app.url'),
                    $channel->server_id,
                    $channel->user
                ),
                TextAttachment::COLOR_DANGER
            ));
            $this->addAttachment(new TextAttachment(
                'Commands for unregistered channels:',
                '· `help` - Show help' . PHP_EOL
                    . '· `XdY[+C] [text]` - Roll X dice with Y sides, '
                    . 'optionally adding C to the result, optionally '
                    . 'describing that the roll is for "text"',
                TextAttachment::COLOR_INFO
            ));
            return;
        }
        $this->addAttachment(new TextAttachment(
            'Commands for unregistered channels:',
            '· `help` - Show help' . PHP_EOL
                . '· `XdY[+C] [text]` - Roll X dice with Y sides, '
                . 'optionally adding C to the result, optionally '
                . 'describing that the roll is for "text"' . PHP_EOL
                . '· `register <system>` - Register this channel for '
                . 'system code <system>',
            TextAttachment::COLOR_INFO
        ));
    }
}
