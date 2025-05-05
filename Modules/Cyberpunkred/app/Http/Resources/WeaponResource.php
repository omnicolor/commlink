<?php

declare(strict_types=1);

namespace Modules\Cyberpunkred\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Cyberpunkred\Models\RangedWeapon;
use Modules\Cyberpunkred\Models\Weapon;

/**
 * @mixin Weapon
 */
class WeaponResource extends JsonResource
{
    /**
     * @return array{
     *     id: string,
     *     type: string,
     *     class: string,
     *     concealable: bool,
     *     cost: int,
     *     damage: string,
     *     examples: array{
     *         poor: array<int, string>,
     *         standard: array<int, string>,
     *         excellent: array<int, string>
     *     },
     *     hands_required: int,
     *     magazine?: int,
     *     name: string,
     *     rate_of_fire: int,
     *     skill: string,
     *     links: array{
     *         self: string
     *     }
     * }
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'class' => $this->resource instanceof RangedWeapon ? 'ranged' : 'melee',
            'concealable' => $this->concealable,
            'cost' => $this->cost,
            'damage' => $this->damage,
            'examples' => [
                'poor' => $this->examples['poor'] ?? [],
                'standard' => $this->examples['standard'] ?? [],
                'excellent' => $this->examples['excellent'] ?? [],
            ],
            'hands_required' => $this->handsRequired,
            'magazine' => $this->when(
                $this->resource instanceof RangedWeapon,
                // @phpstan-ignore property.notFound
                $this->magazine,
            ),
            'name' => $this->name,
            'rate_of_fire' => $this->rateOfFire,
            'skill' => $this->skill,
            'links' => [
                'self' => route('cyberpunkred.weapons.show', $this->id),
            ],
        ];
    }
}
