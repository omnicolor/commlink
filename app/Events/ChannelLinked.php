<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Campaign;
use App\Models\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class ChannelLinked implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event, dispatched when a Channel (like Slack or Discord) are
     * linked to Commlink.
     */
    public function __construct(public readonly Channel $channel)
    {
    }

    /**
     * Get the channel the event should broadcast on.
     */
    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('users.' . $this->channel->registered_by);
    }

    /**
     * Get the data to broadcast.
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        $channel = $this->channel->attributesToArray();
        if (null !== $this->channel->campaign_id) {
            /** @var Campaign */
            $campaign = Campaign::find($this->channel->campaign_id);
            $channel['campaign_name'] = $campaign->name;
        }
        return $channel;
    }
}
