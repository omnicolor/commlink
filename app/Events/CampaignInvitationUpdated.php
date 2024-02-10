<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\CampaignInvitation;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CampaignInvitationUpdated
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
            // @phpstan-ignore-next-line
            'campaigns.' . $this->invitation->campaign->id,
        );
    }
}
