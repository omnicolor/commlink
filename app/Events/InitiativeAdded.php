<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Campaign;
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
     * @var string
     */
    public string $name;

    /**
     * Create a new event instance.
     * @param Initiative $initiative
     * @param Campaign $campaign
     */
    public function __construct(
        public Initiative $initiative,
        public Campaign $campaign
    ) {
        $this->name = (string)$initiative;
    }

    /**
     * Get the channels the event should broadcast on.
     * @return PrivateChannel
     */
    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('campaign.' . $this->campaign->id);
    }
}
