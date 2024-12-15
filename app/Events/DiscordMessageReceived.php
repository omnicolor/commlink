<?php

declare(strict_types=1);

namespace App\Events;

use Discord\Discord;
use Discord\Parts\Channel\Channel;
use Discord\Parts\Channel\Message;
use Discord\Parts\Guild\Guild;
use Discord\Parts\Thread\Thread;
use Discord\Parts\User\User;
use RuntimeException;

use function str_replace;

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
    public User|null $user;

    public function __construct(public Message $message, public Discord $discord)
    {
        if (null === $message->channel || null === $message->channel->guild) {
            throw new RuntimeException('Cannot handle null channels or servers');
        }
        if ($message->channel instanceof Thread) {
            throw new RuntimeException('Cannot handle threads');
        }
        $this->channel = $message->channel;
        $this->content = str_replace('/roll ', '', $message->content);
        $this->user = $message->author;
        $this->server = $message->channel->guild;
    }
}
