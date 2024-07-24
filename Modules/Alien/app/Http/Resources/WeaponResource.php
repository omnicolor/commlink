<?php

declare(strict_types=1);

namespace Modules\Alien\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;
use Modules\Alien\Models\Weapon;

/**
 * @mixin Weapon
 */
class WeaponResource extends JsonResource
{
    /**
     * @return array{
     *   bonus: int,
     *   class: string,
     *   cost: ?int,
     *   damage: ?int,
     *   description: MissingValue|string,
     *   id: string,
     *   links: array{
     *     self: string
     *   },
     *   modifiers: array<int, string>,
     *   name: string,
     *   page: int,
     *   range: string,
     *   ruleset: string,
     *   weight: ?float
     * }
     */
    public function toArray(Request $request): array
    {
        /** @var User */
        $user = $request->user();
        return [
            'bonus' => $this->bonus,
            'class' => $this->class,
            'cost' => $this->cost,
            'damage' => $this->damage,
            'description' => $this->when(
                $user->hasPermissionTo('view data'),
                $this->description,
            ),
            'id' => $this->id,
            'modifiers' => $this->modifiers,
            'name' => $this->name,
            'page' => $this->page,
            'range' => $this->range,
            'ruleset' => $this->ruleset,
            'weight' => $this->weight,
            'links' => [
                'self' => route('alien.weapons.show', $this->id),
            ],
        ];
    }
}
