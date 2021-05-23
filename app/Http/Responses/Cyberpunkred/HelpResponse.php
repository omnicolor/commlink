<?php

declare(strict_types=1);

namespace App\Http\Responses\Cyberpunkred;

use App\Http\Responses\SlackResponse;
use App\Models\Channel;
use App\Models\Slack\TextAttachment;

/**
 * Help for a channel registered for Cyberpunk Red.
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
        ?Channel $channel = null,
    ) {
        parent::__construct($content, $status, $headers, $channel);
        $this->addAttachment(new TextAttachment(
            'Commlink - Cyberpunk Red',
            'Commlink is a Slack bot that lets you roll dice for Cyberpunk Red '
                . 'RPG.' . \PHP_EOL
                . '· `help` - Show help' . \PHP_EOL
                . '· `4 [text]` - Roll 1d10 dice adding 4 to the result, with '
                . 'optional text (automatics, perception, etc), taking into '
                . 'account critical successes and failures',
            TextAttachment::COLOR_INFO
        ));
        if (null !== $channel && null === $channel->character()) {
            $this->addAttachment(new TextAttachment(
                'Unregistered',
                \sprintf(
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
