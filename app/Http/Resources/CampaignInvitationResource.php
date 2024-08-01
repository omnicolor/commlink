<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\CampaignInvitation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin CampaignInvitation
 */
class CampaignInvitationResource extends JsonResource
{
    /**
     * @return array<string, array<string, array<string, array<string, string>|int|string>>>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => [
                'campaign' => [
                    'id' => $this->campaign->id,
                    'name' => $this->campaign->name,
                    'system' => $this->campaign->system,
                    'links' => [
                        'self' => route('campaign.view', $this->campaign),
                    ],
                ],
                'invited_by' => [
                    'id' => $this->invitor->id,
                    'name' => $this->invitor->name,
                ],
                'invitee' => [
                    'email' => $this->email,
                    'name' => $this->name,
                ],
            ],
        ];
    }
}
