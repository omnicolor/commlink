<?php

declare(strict_types=1);

namespace App\Http\Responses\Shadowrun5e;

use App\Exceptions\SlackException;
use App\Http\Responses\SlackResponse;
use App\Models\Channel;
use App\Models\Character;
use App\Models\ChatCharacter;
use App\Models\Slack\TextAttachment;

/**
 * Try to link a character to a Slack channel.
 */
class LinkResponse extends SlackResponse
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
        string $content,
        int $status,
        array $headers,
        Channel $channel,
    ) {
        parent::__construct($content, $status, $headers, $channel);

        /** @var \App\Models\ChatUser */
        $chatUser = $this->channel->getChatUser();
        $this->requireCommlink($chatUser);

        if (null !== $channel->character()) {
            throw new SlackException(
                'This channel is already linked to a character.'
            );
        }

        $args = \explode(' ', $content);
        $characterId = $args[1];
        $character = Character::find($characterId);
        if (null === $character) {
            throw new SlackException(
                'Unable to find one of your characters with that ID.'
            );
        }

        $user = $chatUser->user;
        if (null === $user || $character->owner !== $user->email) {
            throw new SlackException(
                'You don\'t own that character.'
            );
        }

        if ($channel->system !== $character->system) {
            $systems = config('app.systems');
            throw new SlackException(sprintf(
                '%s is a %s character. This channel is playing %s.',
                $character->handle ?? $character->name,
                $systems[$character->system] ?? 'Unknown',
                $systems[$channel->system] ?? 'Unknown',
            ));
        }

        ChatCharacter::create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        $this->addAttachment(new TextAttachment(
            'Linking Character',
            sprintf(
                'You have linked %s to this channel.',
                $character->handle ?? $character->name ?? 'Unknown',
            ),
            TextAttachment::COLOR_INFO,
        ));
    }
}
