<?php

declare(strict_types=1);

namespace App\Http\Responses\Expanse;

use App\Http\Responses\SlackResponse;
use App\Models\Channel;
use App\Models\Slack\TextAttachment;

/**
 * Help for a channel registered for The Expanse.
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
        $this->addAttachment(new TextAttachment(
            'Commlink - The Expanse',
            'Commlink is a Slack bot that lets you roll dice for The Expanse '
                . 'RPG.' . PHP_EOL
                . '· `help` - Show help' . PHP_EOL
                . '· `4 [text]` - Roll 3d6 dice adding 4 to the result, with '
                . 'optional text (automatics, perception, etc)' . PHP_EOL,
            TextAttachment::COLOR_INFO
        ));
        if (!is_null($channel) && is_null($channel->character())) {
            $this->addAttachment(new TextAttachment(
                'Unregistered',
                sprintf(
                    'It doesn\'t look like you\'ve linked a character here. If '
                        . 'you\'ve already built a character in <%s|Commlink>, '
                        . 'type `/roll link <characterId>` to connect your '
                        . 'character here.',
                    config('app.url')
                ),
                TextAttachment::COLOR_INFO
            ));
        }
    }
}
