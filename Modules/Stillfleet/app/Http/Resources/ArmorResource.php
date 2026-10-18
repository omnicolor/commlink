<?php

declare(strict_types=1);

namespace Modules\Stillfleet\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Stillfleet\Models\Armor;
use Override;

/**
 * @mixin Armor
 */
class ArmorResource extends JsonResource
{
    /**
     * @return array{
     *     cost: int,
     *     damage_reduction: int,
     *     id: string,
     *     name: string,
     *     notes: string,
     *     page: int,
     *     ruleset: string,
     *     tech_cost: int,
     *     tech_strata: string,
     *     links: array{
     *         self: string
     *     }
     * }
     */
    #[Override]
    public function toArray(Request $request): array
    {
        return [
            'cost' => $this->cost,
            'damage_reduction' => $this->damage_reduction,
            'id' => $this->id,
            'name' => $this->name,
            'notes' => $this->notes,
            'page' => $this->page,
            'ruleset' => $this->ruleset,
            'tech_cost' => $this->tech_cost,
            'tech_strata' => $this->tech_strata->value,
            'links' => [
                'self' => route('stillfleet.armor.show', $this->id),
            ],
        ];
    }
}
