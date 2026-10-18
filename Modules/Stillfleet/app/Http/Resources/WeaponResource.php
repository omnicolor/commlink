<?php

declare(strict_types=1);

namespace Modules\Stillfleet\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Stillfleet\Models\Weapon;
use Override;

use function route;

/**
 * @mixin Weapon
 */
class WeaponResource extends JsonResource
{
    /**
     * @return array{
     *     damage: string,
     *     id: string,
     *     name: string,
     *     notes?: string,
     *     other_names?: string,
     *     page: int,
     *     price: int|string,
     *     range?: int,
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
        return [
            'damage' => $this->damage,
            'id' => $this->id,
            'name' => $this->name,
            'notes' => $this->when('' !== $this->notes, $this->notes),
            'other_names' => $this->when(
                null !== $this->other_names,
                $this->other_names,
            ),
            'page' => $this->page,
            'price' => $this->price,
            'range' => $this->when(null !== $this->range, (int)$this->range),
            'ruleset' => $this->ruleset,
            'tech_cost' => $this->tech_cost,
            'tech_strata' => $this->tech_strata->value,
            'type' => $this->type->value,
            'links' => [
                'self' => route('stillfleet.weapons.show', $this->id),
            ],
        ];
    }
}
