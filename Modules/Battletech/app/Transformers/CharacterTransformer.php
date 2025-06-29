<?php

declare(strict_types=1);

namespace Modules\Battletech\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Battletech\Models\Appearance;
use Modules\Battletech\Models\Character;
use Modules\Battletech\Models\Quality;
use Modules\Battletech\Models\Skill;
use Modules\Battletech\ValueObjects\Attributes;
use Override;

/**
 * @mixin Character
 * @phpstan-import-type AppearanceArray from Appearance
 * @phpstan-import-type AttributesArray from Attributes
 */
class CharacterTransformer extends JsonResource
{
    /**
     * @return array{
     *     name: string,
     *     appearance: AppearanceArray,
     *     attributes: AttributesArray,
     *     campaign_id?: int,
     *     id: string,
     *     owner: array{
     *         id: int,
     *         name: string
     *     },
     *     skills: AnonymousResourceCollection<Skill>,
     *     system: string,
     *     traits: AnonymousResourceCollection<Quality>,
     *     links: array{
     *         self: string,
     *         campaign?: string
     *     }
     * }
     */
    #[Override]
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'appearance' => $this->appearance->toArray(),
            'attributes' => $this->attributes->toArray(),
            'campaign_id' => $this->when(
                null !== $this->campaign_id,
                $this->campaign_id
            ),
            'id' => $this->id,
            'owner' => [
                'id' => $this->user()->id,
                'name' => $this->user()->name,
            ],
            'skills' => SkillResource::collection($this->skills),
            'system' => $this->system,
            'traits' => TraitResource::collection($this->traits),
            'links' => [
                'campaign' => $this->when(
                    null !== $this->campaign_id,
                    null !== $this->campaign_id
                        ? route('campaigns.show', $this->campaign_id)
                        : null,
                ),
                'self' => route('battletech.characters.show', $this->id),
            ],
        ];
    }
}
