<?php

declare(strict_types=1);

namespace App\Events;

use Discord\Discord;
use Discord\Parts\Channel\Channel;
use Discord\Parts\Channel\Message;
use Discord\Parts\Guild\Guild;
use Discord\Parts\User\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DiscordMessageReceived
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Channel the message was sent on.
     * @var Channel
     */
    public Channel $channel;

    /**
     * Content of the command, without the preceding '/roll '.
     * @var string
     */
    public string $content;

    /**
     * Server the command was received on.
     * @var Guild
     */
    public $server;

    /**
     * User that sent the message we're reacting to.
     * @var User
     */
    public User $user;

    public function __construct(public Message $message, public Discord $discord)
    {
        // @phpstan-ignore-next-line
        $this->channel = $this->message->channel;
        $this->content = \str_replace('/roll ', '', $this->message->content);

        // @phpstan-ignore-next-line
        $this->user = $this->message->author;

        // @phpstan-ignore-next-line
        $this->server = $this->message->channel->guild;
    }
}
