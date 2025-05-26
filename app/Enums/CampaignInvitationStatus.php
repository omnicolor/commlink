<?php

declare(strict_types=1);

namespace App\Enums;

enum CampaignInvitationStatus: string
{
    case Invited = 'invited';
    case Responded = 'responded';
    case Spam = 'spam';
}
