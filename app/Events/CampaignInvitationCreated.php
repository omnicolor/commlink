<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\CampaignInvitation;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CampaignInvitationCreated implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public CampaignInvitation $invitation)
    {
    }

    /**
     * @codeCoverageIgnore
     */
    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel(
            'campaigns.' . $this->invitation->campaign->id,
        );
    }
}
