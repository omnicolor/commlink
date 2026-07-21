<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Transformers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Shadowrun6e\Models\AdeptPower;
use Override;

/**
 * @mixin AdeptPower
 */
class AdeptPowerResource extends JsonResource
{
    /**
     * @return array{
     *     activation: string,
     *     cost: float,
     *     description?: string,
     *     effects: array<int, string>|null,
     *     id: string,
     *     name: string,
     *     page: int,
     *     ruleset: string,
     *     links: array{
     *         self: string,
     *         collection: string
     *     }
     * }
     */
    #[Override]
    public function toArray(Request $request): array
    {
        /** @var User $user */
        $user = $request->user();
        return [
            'activation' => $this->activation,
            'cost' => $this->cost,
            'description' => $this->when(
                $user->hasPermissionTo('view data'),
                $this->description,
            ),
            'effects' => $this->effects,
            'id' => $this->id,
            'name' => $this->name,
            'page' => $this->page,
            'ruleset' => $this->ruleset,
            'links' => [
                'self' => route('shadowrun6e.adept-powers.show', $this->id),
                'collection' => route('shadowrun6e.adept-powers.index'),
            ],
        ];
    }
}
