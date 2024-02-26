<?php

declare(strict_types=1);

namespace App\Events;

use Discord\Discord;
use Discord\Parts\Channel\Channel;
use Discord\Parts\Channel\Message;
use Discord\Parts\Guild\Guild;
use Discord\Parts\User\User;

/**
 * @property Message $message
 */
class DiscordMessageReceived extends MessageReceived
{
    /**
     * Channel the message was sent on.
     */
    public Channel $channel;

    /**
     * Content of the command, without the preceding '/roll '.
     */
    public string $content;

    /**
     * Server the command was received on.
     */
    public Guild $server;

    /**
     * User that sent the message we're reacting to.
     */
    public User $user;

    /**
     * @psalm-suppress PossiblyUnusedMethod
     * @psalm-suppress PossiblyUnusedProperty
     */
    public function __construct(public Message $message, public Discord $discord)
    {
        // @phpstan-ignore-next-line
        $this->channel = $message->channel;

        $this->content = \str_replace('/roll ', '', $message->content);

        // @phpstan-ignore-next-line
        $this->user = $message->author;

        // @phpstan-ignore-next-line
        $this->server = $message->channel->guild;
    }
}
