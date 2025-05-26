<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\CampaignInvitationCreated;
use App\Mail\InvitedToCampaign;
use Illuminate\Support\Facades\Mail;

class SendEmailOnCampaignInvitationCreated
{
    public function handle(CampaignInvitationCreated $event): void
    {
        Mail::to($event->invitation->email->address)
            ->send(new InvitedToCampaign($event->invitation));
    }
}
