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
                    // @phpstan-ignore-next-line
                    'id' => $this->campaign->id,
                    // @phpstan-ignore-next-line
                    'name' => $this->campaign->name,
                    // @phpstan-ignore-next-line
                    'system' => $this->campaign->system,
                    'links' => [
                        'self' => route('campaign.view', $this->campaign),
                    ],
                ],
                'invited_by' => [
                    // @phpstan-ignore-next-line
                    'id' => $this->invitor->id,
                    // @phpstan-ignore-next-line
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
