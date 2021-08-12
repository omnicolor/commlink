<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\ChatUser;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SlackUserLinked
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
}
