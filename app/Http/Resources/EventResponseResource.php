<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\EventRsvp;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin EventRsvp
 */
class EventResponseResource extends JsonResource
{
    /**
     * @return array<string, int|string>
     */
    public function toArray(Request $request): array
    {
        return [
            'user_name' => $this->user->name,
            'user_id' => $this->user_id,
            'response' => $this->response,
        ];
    }
}
