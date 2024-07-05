<?php

declare(strict_types=1);

namespace Modules\Capers\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Capers\Models\Character;

use function array_values;

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
            'background' => $this->background,
            'description' => $this->description,
            'gear' => GearResource::collection((array)$this->gear),
            'mannerisms' => $this->mannerisms,
            'powers' => PowerResource::collection(array_values((array)$this->powers)),
            'identity' => new IdentityResource($this->identity),
            'skills' => SkillResource::collection(array_values(
                (array)$this->skills
            )),
            'stats' => [
                'agility' => $this->agility,
                'charisma' => $this->charisma,
                'expertise' => $this->expertise,
                'perception' => $this->perception,
                'resilience' => $this->resilience,
                'strength' => $this->strength,
            ],
            'type' => $this->type,
            'vice' => new ViceResource($this->vice),
            'virtue' => new VirtueResource($this->virtue),
            'id' => $this->id,
            'campaign_id' => $this->when(
                null !== $this->campaign_id,
                $this->campaign_id,
            ),
            'system' => $this->system,
            'owner' => [
                // @phpstan-ignore-next-line
                'id' => $this->user()->id,
                // @phpstan-ignore-next-line
                'name' => $this->user()->name,
            ],
            'links' => [
                'self' => route('capers.characters.show', $this->id),
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
