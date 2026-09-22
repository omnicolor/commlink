<?php

declare(strict_types=1);

namespace Modules\Stillfleet\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Stillfleet\Models\Gear;
use Override;

/**
 * @mixin Gear
 */
class GearResource extends JsonResource
{
    /**
     * @return array{
     *     description?: string,
     *     id: string,
     *     name: string,
     *     page: int,
     *     price: int,
     *     ruleset: string,
     *     tech_cost: int,
     *     tech_strata: string,
     *     type: string,
     *     links: array{
     *         self: string
     *     }
     * }
     */
    #[Override]
    public function toArray(Request $request): array
    {
        /** @var User */
        $user = $request->user();
        return [
            'description' => $this->when(
                $user->hasPermissionTo('view data'),
                $this->description,
            ),
            'id' => $this->id,
            'name' => $this->name,
            'page' => $this->page,
            'price' => $this->price,
            'ruleset' => $this->ruleset,
            'tech_cost' => $this->tech_cost,
            'tech_strata' => $this->tech_strata->value,
            'type' => $this->type->value,
            'links' => [
                'self' => route('stillfleet.gear.show', $this->id),
            ],
        ];
    }
}
