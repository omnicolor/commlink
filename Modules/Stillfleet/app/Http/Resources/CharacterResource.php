<?php

declare(strict_types=1);

namespace Modules\Stillfleet\Http\Resources;

use App\Http\Resources\UserMinimalResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Stillfleet\Models\Character;
use Override;

/**
 *  @mixin Character
 */
class CharacterResource extends JsonResource
{
    /**
     * @return array{
     *     name: string,
     *     attributes: array{
     *         charm: string,
     *         charm_modifier: int,
     *         combat: string,
     *         combat_modifier: int,
     *         movement: string,
     *         movement_modifier: int,
     *         reason: string,
     *         reason_modifier: int,
     *         will: string,
     *         will_modifier: int,
     *         grit_max: int,
     *         grit_current: int,
     *         health_max: int,
     *         health_current: int
     *     },
     *     gear: AnonymousResourceCollection,
     *     rank: int,
     *     classes: AnonymousResourceCollection,
     *     species: SpeciesResource,
     *     id: string,
     *     owner: UserMinimalResource,
     *     system: string
     * }
     */
    #[Override]
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'attributes' => [
                'charm' => $this->charm,
                'charm_modifier' => $this->charm_modifier,
                'combat' => $this->combat,
                'combat_modifier' => $this->combat_modifier,
                'movement' => $this->movement,
                'movement_modifier' => $this->movement_modifier,
                'reason' => $this->reason,
                'reason_modifier' => $this->reason_modifier,
                'will' => $this->will,
                'will_modifier' => $this->will_modifier,
                'grit_max' => $this->grit,
                'grit_current' => $this->grit_current,
                'health_max' => $this->health,
                'health_current' => $this->health_current,
            ],
            'gear' => GearResource::collection($this->gear),
            'rank' => $this->rank,
            'classes' => RoleResource::collection($this->roles),
            'species' => new SpeciesResource($this->species),
            'id' => $this->id,
            'owner' => new UserMinimalResource($this->user()),
            'system' => 'stillfleet',
        ];
    }
}
