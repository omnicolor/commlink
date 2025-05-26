<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Http\Resources;

use App\Http\Resources\JsonResource;
use Illuminate\Http\Request;
use Modules\Shadowrun5e\Models\Character;

/**
 * @mixin Character
 */
class CharacterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        if (isset($this->priorities['a'])) {
            $priorities = $this->priorities;
        } elseif (isset($this->priorities['attributePriority'])) {
            $priorities = [
                'attribute_priority' => $this->priorities['attributePriority'],
                'gameplay' => $this->priorities['gameplay'],
                'magic' => $this->priorities['magic'],
                'magic_priority' => $this->priorities['magicPriority'],
                'metatype' => $this->metatype,
                'metatype_priority' => $this->priorities['metatypePriority'],
                'resource_priority' => $this->priorities['resourcePriority'],
                'skill_priority' => $this->priorities['skillPriority'],
            ];
        } else {
            $priorities = null;
        }
        return [
            'handle' => $this->handle,
            'damage_overflow' => $this->damageOverflow,
            'damage_physical' => $this->damagePhysical,
            'damage_stun' => $this->damageStun,
            'priorities' => $priorities,
            'foo' => $this->priorities,
            'id' => $this->id,
            'campaign_id' => $this->when(
                null !== $this->campaign_id,
                $this->campaign_id,
            ),
            'system' => $this->system,
            'owner' => [
                'id' => $this->user()->id,
                'name' => $this->user()->name,
            ],
            'links' => [
                'self' => route('shadowrun5e.characters.show', $this->id),
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
