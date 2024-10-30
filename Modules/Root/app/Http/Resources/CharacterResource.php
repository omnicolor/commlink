<?php

declare(strict_types=1);

namespace Modules\Root\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;
use Modules\Root\Models\Character;

/**
 * @mixin Character
 */
class CharacterResource extends JsonResource
{
    /**
     * @return array{
     *   name: string,
     *   meta: array{
     *     look: string,
     *     species: string
     *   },
     *   stats: array{
     *     charm: int,
     *     cunning: int,
     *     finese: int,
     *     luck: int,
     *     might: int
     *   },
     *   id: string,
     *   campaign_id: MissingValue|integer,
     *   system: string,
     *   owner: array{
     *     id: integer,
     *     name: string
     *   },
     *   links: array{
     *     self: string,
     *     campaign: MissingValue|string
     *   }
     * }
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'meta' => [
                'look' => $this->look,
                'species' => $this->species,
            ],
            'stats' => [
                'charm' => $this->charm->value,
                'cunning' => $this->cunning->value,
                'finese' => $this->finese->value,
                'luck' => $this->luck->value,
                'might' => $this->might->value,
            ],
            'id' => $this->id,
            'campaign_id' => $this->when(
                null !== $this->campaign_id,
                $this->campaign_id,
            ),
            'system' => $this->system,
            'owner' => [
                // @phpstan-ignore staticMethod.dynamicCall
                'id' => $this->user()->id,
                // @phpstan-ignore staticMethod.dynamicCall
                'name' => $this->user()->name,
            ],
            'links' => [
                'self' => route('alien.characters.show', $this->id),
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
