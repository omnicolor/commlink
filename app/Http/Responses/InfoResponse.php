<?php

declare(strict_types=1);

namespace App\Http\Responses;

use App\Models\Channel;
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
        $chatUser = $this->channel->getChatUser();
        $commlinkUser = 'Not linked';
        if (null !== $chatUser && null !== $chatUser->user) {
            $commlinkUser = $chatUser->user->email;
        }
        $attachment = (new FieldsAttachment('Debugging Info'))
            ->addField(new Field('Team ID', $this->channel->server_id))
            ->addField(new Field('Channel ID', $this->channel->channel_id))
            ->addField(new Field('User ID', $this->channel->user ?? ''))
            ->addField(new Field(
                'Commlink User',
                $commlinkUser ?? 'Not linked'
            ))
            ->addField(new Field(
                'System',
                config('app.systems')[$this->channel->system] ?? $this->channel->system ?? 'unregistered'
            ));
        $this->addAttachment($attachment);
    }
}
