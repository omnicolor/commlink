<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\CampaignInvitationCreated;
use App\Mail\InvitedToCampaign;
use Illuminate\Support\Facades\Mail;

class SendEmailOnCampaignInvitationCreated
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function handle(CampaignInvitationCreated $event): void
    {
        Mail::to($event->invitation->email)
            ->send(new InvitedToCampaign($event->invitation));
    }
}
