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
            'Commlink is a Slack bot that lets you roll Shadowrun 5e dice.',
            TextAttachment::COLOR_INFO
        ));
        $this->addAttachment(new TextAttachment(
            'Misc Commands',
            '`help` - Show help',
            TextAttachment::COLOR_INFO
        ));
    }
}
