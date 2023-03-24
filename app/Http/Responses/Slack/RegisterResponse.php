<?php

declare(strict_types=1);

namespace App\Http\Responses\Slack;

use App\Events\ChannelLinked;
use App\Exceptions\SlackException;
use App\Models\Channel;
use App\Models\Slack\TextAttachment;

/**
 * Slack response for registering a channel for a particular system.
 */
class RegisterResponse extends SlackResponse
{
    protected const MIN_NUM_ARGUMENTS = 2;

    /**
     * Constructor.
     * @param string $content
     * @param int $status
     * @param array<string, string> $headers
     * @param ?Channel $channel
     * @throws SlackException
     */
    public function __construct(
        string $content = '',
        int $status = self::HTTP_OK,
        array $headers = [],
        ?Channel $channel = null,
    ) {
        parent::__construct('', $status, $headers, $channel);
        if (null === $channel) {
            throw new SlackException('Channel is required');
        }
        if (null !== $channel->system) {
            throw new SlackException(\sprintf(
                'This channel is already registered for "%s"',
                $channel->system
            ));
        }
        $chatUser = $this->channel->getChatUser();
        if (null === $chatUser) {
            throw new SlackException(\sprintf(
                'You must have already created an account on <%s|%s> and '
                    . 'linked it to this server before you can register a '
                    . 'channel to a specific system.',
                config('app.url'),
                config('app.name')
            ));
        }

        $systems = config('app.systems');
        $args = \explode(' ', $content);
        if (self::MIN_NUM_ARGUMENTS !== \count($args)) {
            throw new SlackException(\sprintf(
                'To register a channel, use `register [system]`, where system '
                    . 'is a system code: %s',
                \implode(', ', \array_keys($systems))
            ));
        }
        if (!\array_key_exists($args[1], $systems)) {
            throw new SlackException(\sprintf(
                '"%s" is not a valid system code. Use `register [system]`, '
                . 'where system is: %s',
                $args[1],
                \implode(', ', \array_keys($systems))
            ));
        }
        $channel->server_name = $channel->getSlackTeamName($channel->server_id);
        $channel->channel_name = $channel->getSlackChannelName($channel->channel_id);
        // @phpstan-ignore-next-line
        $channel->registered_by = $chatUser->user->id;
        $channel->system = $args[1];
        $channel->save();
        $this->addAttachment(new TextAttachment(
            'Registered',
            \sprintf(
                '%s has registered this channel for the "%s" system.',
                $channel->username,
                $systems[$args[1]]
            ),
            TextAttachment::COLOR_SUCCESS
        ))
            ->sendToChannel();
        ChannelLinked::dispatch($channel);
    }
}
