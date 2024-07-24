<?php

declare(strict_types=1);

namespace Modules\Alien\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;
use Modules\Alien\Models\Character;

/**
 * @mixin Character
 */
class CharacterResource extends JsonResource
{
    /**
     * @return array{
     *   name: string,
     *   stats: array{
     *     agility: int,
     *     empathy: int,
     *     strength: int,
     *     wits: int
     *   },
     *   appearance: ?string,
     *   armor: ArmorResource,
     *   buddy: string,
     *   career: CareerResource,
     *   cash: int,
     *   encumbrance: int,
     *   encumbrance_maximum: int,
     *   gear: AnonymousResourceCollection,
     *   health_current: int,
     *   health_maximum: int,
     *   injuries: AnonymousResourceCollection,
     *   radiation: int,
     *   rival: string,
     *   skills: AnonymousResourceCollection,
     *   talents: AnonymousResourceCollection,
     *   weapons: AnonymousResourceCollection,
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
            'stats' => [
                'agility' => $this->agility,
                'empathy' => $this->empathy,
                'strength' => $this->strength,
                'wits' => $this->wits,
            ],
            'appearance' => $this->appearance,
            'armor' => new ArmorResource($this->armor),
            'buddy' => $this->buddy,
            'career' => new CareerResource($this->career),
            'cash' => $this->cash,
            'encumbrance' => $this->encumbrance,
            'encumbrance_maximum' => $this->encumbrance_maximum,
            'gear' => GearResource::collection($this->gear),
            'health_current' => $this->health_current,
            'health_maximum' => $this->health_maximum,
            'injuries' => InjuryResource::collection($this->injuries),
            'radiation' => $this->radiation,
            'rival' => $this->rival,
            'skills' => SkillResource::collection(array_values($this->skills)),
            'talents' => TalentResource::collection($this->talents),
            'weapons' => WeaponResource::collection($this->weapons),
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
