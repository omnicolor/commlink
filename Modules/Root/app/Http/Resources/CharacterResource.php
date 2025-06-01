<?php

declare(strict_types=1);

namespace Modules\Root\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;
use Modules\Root\Models\Character;
use Modules\Root\Models\Move;
use Override;

use function route;

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
     *     finesse: int,
     *     luck: int,
     *     might: int
     *   },
     *   moves: AnonymousResourceCollection<Move>,
     *   nature: NatureResource,
     *   playbook: PlaybookResource,
     *   tracks: array{
     *     decay: int<0, 5>,
     *     decay_max: int<0, 5>,
     *     exhaustion: int<0, 5>,
     *     exhaustion_max: int<0, 5>,
     *     injury: int<0, 5>,
     *     injury_max: int<0, 5>,
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
    #[Override]
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
                'finesse' => $this->finesse->value,
                'luck' => $this->luck->value,
                'might' => $this->might->value,
            ],
            'moves' => MoveResource::collection($this->moves->values()),
            'nature' => new NatureResource($this->nature),
            'playbook' => new PlaybookResource($this->playbook),
            'tracks' => [
                'decay' => $this->decay,
                'decay_max' => $this->decay_max,
                'exhaustion' => $this->exhaustion,
                'exhaustion_max' => $this->exhaustion_max,
                'injury' => $this->injury,
                'injury_max' => $this->injury_max,
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
