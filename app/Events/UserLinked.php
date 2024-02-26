<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\ChatUser;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserLinked implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public ChatUser $chat_user)
    {
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('users.' . $this->chat_user->user_id);
    }
}
