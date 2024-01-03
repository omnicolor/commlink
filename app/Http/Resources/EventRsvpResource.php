<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\EventRsvp;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin EventRsvp
 */
class EventRsvpResource extends JsonResource
{
    /**
     * @return array<string, array<string, mixed>>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => [
                'event' => [
                    'id' => $this->event->id,
                    'name' => $this->event->name,
                    'real_start' => $this->event->real_start,
                ],
                'response' => $this->response ?? 'tentative',
            ],
            'links' => [
                'campaign' => sprintf(
                    '/api/campaigns/%d',
                    $this->event->campaign->id,
                ),
                'event' => sprintf('/api/events/%d', $this->event->id),
                'root' => '/api',
                'self' => sprintf('/api/events/%d/rsvp', $this->event->id),
                'user' => sprintf('/api/users/%d', $this->user->id),
            ],
        ];
    }
}
