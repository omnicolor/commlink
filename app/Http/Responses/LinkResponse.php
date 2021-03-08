<?php

declare(strict_types=1);

namespace App\Http\Responses;

use App\Exceptions\SlackException;
use App\Models\Character;
use App\Models\Slack\Channel;
use App\Models\Slack\TextAttachment;
use App\Models\SlackLink;

/**
 * Slack response for registering a character to the current channel.
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
        string $content = '',
        int $status = 200,
        array $headers = [],
        Channel $channel = null
    ) {
        parent::__construct('', $status, $headers, $channel);
        if (is_null($channel)) {
            throw new SlackException(('Channel doesn\'t exist.'));
        }
        if ('unregistered' === $channel->system) {
            throw new SlackException(sprintf(
                'This channel isn\'t registered for a system yet. Use '
                    . '`register [system]` before trying to link characters.'
            ));
        }

        $args = explode(' ', $content);
        if (2 !== count($args)) {
            throw new SlackException(
                'To link a character to this channel, use `link [characterId]`.'
            );
        }
        $slackLink = SlackLink::where('slack_team', $channel->team)
            ->where('slack_user', $channel->user)
            ->first();
        if (is_null($slackLink)) {
            throw new SlackException(sprintf(
                'It doesn\'t look like you\'ve registered this channel with '
                    . '<%s|Commlink>. You need to add this Slack Team (%s) and '
                    . 'your Slack User (%s) before you can link a character.',
                config('app.url'),
                $channel->team,
                $channel->user
            ));
        }
        $characterId = $args[1];
        $character = Character::find($characterId);
        if (is_null($character)) {
            throw new SlackException(
                'Could not find a character with that ID.'
            );
        }
        $user = $slackLink->user;
        if (is_null($user) || $user->email != $character->owner) {
            throw new SlackException('You don\'t own that character.');
        }
        if (!array_key_exists($character->system, config('app.systems'))) {
            throw new SlackException(sprintf(
                '"%s" is registered for a system (%s) that this server does '
                    . 'not support.',
                $character->handle ?? $character->name,
                $character->system
            ));
        }
        if ($channel->system !== $character->system) {
            throw new SlackException(sprintf(
                '"%s" is a character for %s, but this channel is registered to play %s.',
                $character->handle ?? $character->name,
                config('app.systems')[$character->system],
                config('app.systems')[$channel->system]
            ));
        }
        if (!is_null($slackLink->character())) {
            throw new SlackException(sprintf(
                'This channel is already linked to a character: "%s". You can '
                    . 'unlink it with `/roll unlink`, then try again to link '
                    . '"%s".',
                $slackLink->character()->handle ?? $slackLink->character()->name,
                $character->handle ?? $character->name
            ));
        }

        $slackLink->character_id = $characterId;
        $slackLink->save();

        $this->addAttachment(new TextAttachment(
            'Linked',
            sprintf(
                'Character "%s" linked for %s',
                $character->handle,
                $channel->username
            ),
            TextAttachment::COLOR_SUCCESS
        ))
            ->sendToChannel();
    }
}
