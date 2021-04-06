<?php

declare(strict_types=1);

namespace App\Events;

use CharlotteDunois\Yasmin\Interfaces\TextChannelInterface;
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
     * @var TextChannelInterface
     */
    public TextChannelInterface $channel;

    /**
     * Content of the command, without the preceding '/roll '.
     * @var string
     */
    public string $content;

    /**
     * Message received from Discord.
     * @var Message
     */
    public Message $message;

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
    public function __construct(Message $message)
    {
        $this->channel = $message->channel;
        $this->content = str_replace('/roll ', '', $message->content);
        $this->message = $message;
        $this->user = $message->author;

        // @phpstan-ignore-next-line
        $this->server = $message->channel->guild;
    }
}
