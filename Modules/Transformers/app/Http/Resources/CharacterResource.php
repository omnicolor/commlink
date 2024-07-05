<?php

declare(strict_types=1);

namespace Modules\Transformers\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Transformers\Models\Character;

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
            'allegiance' => $this->allegiance,
            'alt_mode' => $this->alt_mode,
            'campaign_id' => $this->when(
                null !== $this->campaign_id,
                $this->campaign_id,
            ),
            'color_primary' => $this->color_primary,
            'color_secondary' => $this->color_secondary,
            'courage_alt' => $this->courage_alt,
            'courage_robot' => $this->courage_robot,
            'endurance_alt' => $this->endurance_alt,
            'endurance_robot' => $this->endurance_robot,
            'energon_base' => $this->energon_base,
            'energon_current' => $this->energon_current,
            'firepower_alt' => $this->firepower_alt,
            'firepower_robot' => $this->firepower_robot,
            'hp_base' => $this->hp_base,
            'hp_current' => $this->hp_current,
            'intelligence_alt' => $this->intelligence_alt,
            'intelligence_robot' => $this->intelligence_robot,
            'mode' => $this->mode,
            'programming' => $this->programming,
            'quote' => $this->quote,
            'rank_alt' => $this->rank_alt,
            'rank_robot' => $this->rank_robot,
            'size' => $this->size,
            'skill_alt' => $this->skill_alt,
            'skill_robot' => $this->skill_robot,
            'speed_alt' => $this->speed_alt,
            'speed_robot' => $this->speed_robot,
            'strength_alt' => $this->strength_alt,
            'strength_robot' => $this->strength_robot,
            'subgroups' => SubgroupResource::collection((array)$this->subgroups),
            'weapons' => WeaponResource::collection((array)$this->weapons),
            'id' => $this->id,
            'owner' => [
                // @phpstan-ignore-next-line
                'id' => $this->user()->id,
                // @phpstan-ignore-next-line
                'name' => $this->user()->name,
            ],
            'system' => $this->system,
            'links' => [
                'self' => route('transformers.characters.show', $this->id),
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
