<?php

declare(strict_types=1);

namespace Modules\Battletech\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Battletech\Models\Character;

/**
 * @mixin Character
 */
class CharacterTransformer extends JsonResource
{
    /**
     * @return array{
     *     name: string,
     *     attributes: array{
     *         body: int,
     *         charisma: int,
     *         dexterity: int,
     *         edge: int,
     *         intelligence: int,
     *         reflexes: int,
     *         strength: int,
     *         willpower: int
     *     },
     *     campaign_id?: int,
     *     id: string,
     *     owner: array{
     *         id: int,
     *         name: string
     *     },
     *     system: string,
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
            'system' => $this->system,
            'links' => [
                'campaign' => $this->when(
                    null !== $this->campaign_id,
                    null !== $this->campaign_id
                        ? route('campaigns.show', $this->campaign_id)
                        : null,
                ),
                'self' => '', //route('battletech.characters.show', $this->id),
            ],
        ];
    }
}
