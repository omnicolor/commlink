<?php

declare(strict_types=1);

namespace Modules\Dnd5e\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Dnd5e\Models\Character;

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
            'id' => $this->id,
            'name' => $this->name,
            'campaign_id' => $this->campaign_id,
            'owner' => [
                // @phpstan-ignore-next-line
                'id' => $this->user()->id,
                // @phpstan-ignore-next-line
                'name' => $this->user()->name,
            ],
            'system' => $this->system,
            'links' => [
                'self' => route('dnd5e.characters.show', $this->id),
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
