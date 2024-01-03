<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use function sprintf;

/**
 * @mixin Event
 */
class EventResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => [
                'campaign' => [
                    'id' => $this->campaign->id,
                    'name' => $this->campaign->name,
                ],
                'created_by' => [
                    'id' => $this->creator->id,
                    'name' => $this->creator->name,
                ],
                'description' => $this->description,
                'game_end' => $this->game_end,
                'game_start' => $this->game_start,
                'id' => $this->id,
                'name' => $this->name,
                'real_end' => $this->real_end,
                'real_start' => $this->real_start,
                'responses' => EventResponseResource::collection($this->responses),
                'links' => [
                    'self' => sprintf('/events/%d', $this->id),
                    'campaign' => sprintf('/campaigns/%d', $this->campaign_id),
                    'campaign_collection' => sprintf(
                        '/campaigns/%d/events',
                        $this->campaign_id,
                    ),
                    'event_collection' => '/events',
                    'root' => '/',
                ],
            ],
            'links' => [
                'root' => '/',
                'collection' => '/events',
            ],
        ];
    }
}
