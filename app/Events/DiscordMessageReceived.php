<?php

declare(strict_types=1);

namespace App\Events;

use CharlotteDunois\Yasmin\Models\Guild;
use CharlotteDunois\Yasmin\Models\Message;
use CharlotteDunois\Yasmin\Models\TextChannel;
use CharlotteDunois\Yasmin\Models\User;
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
     * @var TextChannel
     */
    public TextChannel $channel;

    /**
     * Content of the command, without the preceding '/roll '.
     * @var string
     */
    public string $content;

    /**
     * Server the command was received on.
     * @var Guild
     */
    public Guild $server;

    /**
     * User that sent the message we're reacting to.
     * @var User
     */
    public User $user;

    /**
     * Create a new event instance.
     * @param Message $message
     */
    public function __construct(public Message $message)
    {
        // @phpstan-ignore-next-line
        $this->channel = $this->message->channel;
        $this->content = \str_replace('/roll ', '', $this->message->content);
        $this->user = $this->message->author;

        // @phpstan-ignore-next-line
        $this->server = $this->message->channel->guild;
    }
}
