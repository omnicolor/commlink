<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Event;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * An event (a calendar event, not a Laravel event) was scheduled.
 */
class EventCreated
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public Event $event)
    {
    }
}
