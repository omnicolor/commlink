<?php

declare(strict_types=1);

namespace Modules\Capers\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Capers\Models\Power;

use function array_values;

/**
 * @mixin Power
 */
class PowerResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var User */
        $user = $request->user();
        return [
            'name' => $this->name,
            'activation' => $this->activation,
            'available_boosts' => BoostResource::collection(array_values(
                (array)$this->availableBoosts
            )),
            'boosts' => BoostResource::collection((array)$this->boosts),
            'description' => $this->when(
                $user->hasPermissionTo('view data'),
                $this->description,
            ),
            'duration' => $this->duration,
            'effect' => $this->when(
                $user->hasPermissionTo('view data'),
                $this->effect,
            ),
            'id' => $this->id,
            'max_rank' => $this->maxRank,
            'range' => $this->range,
            'target' => $this->target,
            'type' => $this->type,
            'rank' => $this->rank,
            'links' => [
                'self' => route('capers.powers.show', $this->id),
            ],
        ];
    }
}
