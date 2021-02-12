<?php

declare(strict_types=1);

namespace App\Http\Responses;

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
     */
    public function __construct(
        string $content = '',
        int $status = 200,
        array $headers = []
    ) {
        parent::__construct($content, $status, $headers);
        $this->addAttachment(new TextAttachment(
            'About Commlink',
            sprintf(
                'RollBot is a Slack bot that lets you roll dice '
                    . 'appropriate for various RPG systems. For example, if '
                    . 'you are playing The Expanse, it will roll three dice, '
                    . 'marking one of them as the "drama die", adding up the '
                    . 'result with the number you give for your '
                    . 'attribute+focus score, and return the result along with '
                    . 'any stunt points.' . PHP_EOL . PHP_EOL
                    . 'If your game uses <%s|Commlink> as well, links in the '
                    . 'app will automatically roll in Slack, and changes made '
                    . 'to your character via Slack will appear in Commlink.',
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
        $this->addAttachment(new TextAttachment(
            'Commands For Unregistered Channels',
            '· `help` - Show help' . PHP_EOL
                . '· `XdY[+C] [text]` - Roll X dice with Y sides, optionally '
                . 'adding C to the result, optionally describing that the roll '
                . 'is for "text"' . PHP_EOL,
            TextAttachment::COLOR_INFO
        ));
    }
}
