<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\CampaignInvitation;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class CampaignInvitationUpdated
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public readonly CampaignInvitation $invitation)
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
