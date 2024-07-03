<?php

declare(strict_types=1);

namespace Modules\Avatar\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Avatar\Models\Character;

/**
 * @mixin Character
 */
class CharacterResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'appearance' => $this->appearance,
            'background' => ucfirst($this->background->value),
            'creativity' => $this->creativity,
            //'era' => $this->era,
            'fatigue' => $this->fatigue,
            'focus' => $this->focus,
            'harmony' => $this->harmony,
            'history' => $this->history,
            'passion' => $this->passion,
            'campaign_id' => $this->when(
                null !== $this->campaign_id,
                $this->campaign_id
            ),
            'id' => $this->id,
            'owner' => [
                // @phpstan-ignore-next-line
                'id' => $this->user()->id,
                // @phpstan-ignore-next-line
                'name' => $this->user()->name,
            ],
            'system' => $this->system,
            'links' => [
                'self' => route('avatar.characters.show', $this->id),
                'campaign' => $this->when(
                    null !== $this->campaign_id,
                    null !== $this->campaign_id
                        ? route('campaigns.show', $this->campaign_id)
                        : null,
                ),
            ],
        ];
    }
}
