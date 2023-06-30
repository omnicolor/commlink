<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Campaign;
use App\Models\Channel;
use App\Models\Initiative;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InitiativeAdded implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Name of the combatant.
     * @psalm-suppress PossiblyUnusedProperty
     */
    public string $name;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public Initiative $initiative,
        public Campaign $campaign,
        public ?Channel $source = null
    ) {
        $this->name = (string)$initiative;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('campaign.' . $this->campaign->id);
    }
}
