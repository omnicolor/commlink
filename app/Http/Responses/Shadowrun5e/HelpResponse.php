<?php

declare(strict_types=1);

namespace App\Http\Responses\Shadowrun5e;

use App\Http\Responses\SlackResponse;
use App\Models\Slack\TextAttachment;

/**
 * Help for a channel registered as Shadowrun 5E.
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
            'Commlink - Shadowrun 5E',
            'Commlink is a Slack bot that lets you roll Shadowrun 5E dice.'
                . PHP_EOL
                . '· `6 [text]` - Roll 6 dice, with optional text (automatics, '
                . 'perception, etc)' . PHP_EOL
                . '· `12 6 [text]` - Roll 12 dice with a limit of 6' . PHP_EOL
                . '· `XdY[+C] [text]` - Roll X dice with Y sides, optionally '
                . 'adding C to the result, optionally describing that the roll '
                . 'is for "text"' . PHP_EOL,
            TextAttachment::COLOR_INFO
        ));
    }
}
