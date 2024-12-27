<?php

declare(strict_types=1);

namespace Modules\Root\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;
use Modules\Root\Models\Move;
use Modules\Root\Models\Nature;
use Modules\Root\Models\Playbook;

/**
 * @mixin Playbook
 */
class PlaybookResource extends JsonResource
{
    /**
     * @return array{
     *     id: string,
     *     name: string,
     *     description_long: MissingValue|string,
     *     description_short: MissingValue|string,
     *     moves: AnonymousResourceCollection<Move>,
     *     natures: AnonymousResourceCollection<Nature>,
     *     stats: array{
     *         charm: int,
     *         cunning: int,
     *         finesse: int,
     *         luck: int,
     *         might: int,
     *     },
     *     starting_weapon_moves: AnonymousResourceCollection<Move>,
     *     links: array{
     *         self: string
     *     }
     * }
     */
    public function toArray(Request $request): array
    {
        /** @var User */
        $user = $request->user();
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description_long' => $this->when(
                $user->hasPermissionTo('view data'),
                $this->description_long,
            ),
            'description_short' => $this->when(
                $user->hasPermissionTo('view data'),
                $this->description_short,
            ),
            'moves' => MoveResource::collection($this->moves),
            'natures' => NatureResource::collection($this->natures),
            'stats' => [
                'charm' => $this->charm->value,
                'cunning' => $this->cunning->value,
                'finesse' => $this->finesse->value,
                'luck' => $this->finesse->value,
                'might' => $this->might->value,
            ],
            'starting_weapon_moves' => MoveResource::collection($this->starting_weapon_moves),
            'links' => [
                'self' => route('root.playbooks.show', $this->id),
            ],
        ];
    }
}
