<?php

declare(strict_types=1);

namespace Modules\Capers\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Capers\Models\Character;
use Modules\Capers\Models\Gear;
use Modules\Capers\Models\Power;
use Modules\Capers\Models\Skill;

use function array_values;

/**
 * @mixin Character
 */
class CharacterResource extends JsonResource
{
    /**
     * @return array{
     *     name: string,
     *     background: string,
     *     description: string,
     *     gear: AnonymousResourceCollection<Gear>,
     *     mannerisms: string,
     *     powers: AnonymousResourceCollection<Power>,
     *     identity: IdentityResource,
     *     skills: AnonymousResourceCollection<Skill>,
     *     stats: array{
     *         agility: int,
     *         charisma: int,
     *         expertise: int,
     *         perception: int,
     *         resilience: int,
     *         strength: int
     *     },
     *     type: string,
     *     vice: ViceResource,
     *     virtue: VirtueResource,
     *     id: string,
     *     campaign_id?: int,
     *     system: string,
     *     owner: array{
     *         id: int,
     *         name: string
     *     },
     *     links: array{
     *         self: string,
     *         campaign?: string
     *     }
     * }
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
                'id' => $this->user()->id,
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
