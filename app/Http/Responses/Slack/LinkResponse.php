<?php

declare(strict_types=1);

namespace App\Http\Responses\Slack;

use App\Exceptions\SlackException;
use App\Models\Channel;
use App\Models\Character;
use App\Models\ChatCharacter;
use App\Models\ChatUser;
use App\Models\Slack\TextAttachment;

use function explode;

/**
 * Try to link a character to a Slack channel.
 * @psalm-suppress UnusedClass
 */
class LinkResponse extends SlackResponse
{
    /**
     * Constructor.
     * @param array<string, string> $headers
     * @throws SlackException
     */
    public function __construct(
        string $content = '',
        int $status = self::HTTP_OK,
        array $headers = [],
        ?Channel $channel = null,
    ) {
        parent::__construct($content, $status, $headers, $channel);

        /** @var ChatUser */
        $chatUser = $this->channel->getChatUser();
        $this->requireCommlink($chatUser);

        // @phpstan-ignore-next-line
        if (null !== $channel->character()) {
            throw new SlackException(
                'This channel is already linked to a character.'
            );
        }

        $args = explode(' ', $content);
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

        // @phpstan-ignore-next-line
        if ($channel->system !== $character->system) {
            $systems = config('app.systems');
            throw new SlackException(sprintf(
                '%s is a %s character. This channel is playing %s.',
                $character->handle ?? $character->name,
                $systems[$character->system] ?? 'Unknown',
                // @phpstan-ignore-next-line
                $systems[$channel->system] ?? 'Unknown',
            ));
        }

        ChatCharacter::create([
            // @phpstan-ignore-next-line
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
