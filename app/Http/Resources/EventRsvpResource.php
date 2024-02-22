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
                'campaign' => route(
                    'campaigns.show',
                    ['campaign' => $this->event->campaign->id],
                ),
                'event' => route('events.show', ['event' => $this->event]),
                'self' => route('events.rsvp.show', ['event' => $this->event]),
                'user' => route('users.show', [$this->user]),
            ],
        ];
    }
}
