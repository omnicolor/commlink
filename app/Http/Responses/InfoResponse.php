<?php

declare(strict_types=1);

namespace App\Http\Responses;

use App\Models\Slack\Channel;
use App\Models\Slack\Field;
use App\Models\Slack\FieldsAttachment;

/**
 * Slack response to return information about the channel.
 */
class InfoResponse extends SlackResponse
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
        $attachment = (new FieldsAttachment('Debugging Info'))
            ->addField(new Field('Team ID', $this->channel->team))
            ->addField(new Field('Channel ID', $this->channel->channel))
            ->addField(new Field('User ID', $this->channel->user))
            ->addField(new Field(
                'System',
                config('app.systems')[$this->channel->system] ?? $this->channel->system
            ));
        $this->addAttachment($attachment);
    }
}
