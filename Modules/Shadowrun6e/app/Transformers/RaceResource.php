<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Transformers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Shadowrun6e\Models\Race;
use Override;

/**
 * @mixin Race
 */
class RaceResource extends JsonResource
{
    /**
     * @return array{
     *     agility: array{min: int, max: int},
     *     body: array{min: int, max: int},
     *     charisma: array{min: int, max: int},
     *     dermal_armor: int|null,
     *     description?: string,
     *     edge: array{min: int, max: int},
     *     id: string,
     *     intuition: array{min: int, max: int},
     *     logic: array{min: int, max: int},
     *     name: string,
     *     page: int,
     *     reach: int|null,
     *     reaction: array{min: int, max: int},
     *     ruleset: string,
     *     special_points: array{
     *         A?: int,
     *         B?: int,
     *         C: int,
     *         D: int,
     *         E: int
     *     },
     *     strength: array{min: int, max: int},
     *     vision: string|null,
     *     willpower: array{min: int, max: int}
     * }
     */
    #[Override]
    public function toArray(Request $request): array
    {
        /** @var User $user */
        $user = $request->user();
        return [
            'agility' => [
                'min' => $this->agi_min,
                'max' => $this->agi_max,
            ],
            'body' => [
                'min' => $this->bod_min,
                'max' => $this->bod_max,
            ],
            'charisma' => [
                'min' => $this->cha_min,
                'max' => $this->cha_max,
            ],
            'dermal_armor' => $this->dermal_armor,
            'description' => $this->when(
                $user->hasPermissionTo('view data'),
                $this->description,
            ),
            'edge' => [
                'min' => $this->edg_min,
                'max' => $this->edg_max,
            ],
            'id' => $this->id,
            'intuition' => [
                'min' => $this->int_min,
                'max' => $this->int_max,
            ],
            'logic' => [
                'min' => $this->log_min,
                'max' => $this->log_max,
            ],
            'name' => $this->name,
            'page' => $this->page,
            'reach' => $this->reach,
            'reaction' => [
                'min' => $this->rea_min,
                'max' => $this->rea_max,
            ],
            'ruleset' => $this->ruleset,
            'special_points' => $this->special_points,
            'strength' => [
                'min' => $this->str_min,
                'max' => $this->str_max,
            ],
            'vision' => $this->vision,
            'willpower' => [
                'min' => $this->wil_min,
                'max' => $this->wil_max,
            ],
            'links' => [
                'self' => route('shadowrun6e.races.show', $this->id),
                'collection' => route('shadowrun6e.races.index'),
            ],
        ];
    }
}
