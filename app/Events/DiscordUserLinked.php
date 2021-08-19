<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\ChatUser;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DiscordUserLinked implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     * @param ChatUser $chatUser
     */
    public function __construct(public ChatUser $chatUser)
    {
    }

    /**
     * Get the channels the event should broadcast on.
     * @return PrivateChannel
     */
    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('users.' . $this->chatUser->user_id);
    }
}
