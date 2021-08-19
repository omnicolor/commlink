<?php

declare(strict_types=1);

namespace App\Http\Responses\Slack;

use App\Models\Channel;
use App\Models\ChatCharacter;
use App\Models\ChatUser;
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
        ?Channel $channel = null,
    ) {
        parent::__construct($content, $status, $headers, $channel);
        $chatUser = $this->channel->getChatUser();
        $commlinkUser = 'Not linked';
        if (null !== $chatUser && null !== $chatUser->user) {
            $commlinkUser = $chatUser->user->email;
        }
        $campaign = 'No campaign';
        // @phpstan-ignore-next-line
        if (null !== $channel->campaign) {
            // @phpstan-ignore-next-line
            $campaign = $channel->campaign->name;
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
            ))
            ->addField(new Field(
                'Character',
                $this->getCharacterName($chatUser, $channel)
            ))
            ->addField(new Field('Campaign', $campaign));
        $this->addAttachment($attachment);
    }

    /**
     * Return the character's name that is linked to the channel, or null.
     * @param ?ChatUser $user
     * @param ?Channel $channel
     * @return string
     */
    protected function getCharacterName(
        ?ChatUser $user,
        ?Channel $channel
    ): string {
        if (null === $user || null === $channel) {
            return 'No character';
        }
        $chatCharacter = ChatCharacter::where('channel_id', $channel->id)
            ->where('chat_user_id', $user->id)
            ->first();
        if (null === $chatCharacter) {
            return 'No character';
        }
        $character = $chatCharacter->getCharacter();
        if (null === $character) {
            return 'Invalid character';
        }
        return $character->handle ?? $character->name;
    }
}
