<?php

declare(strict_types=1);

namespace Modules\Alien\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;
use Modules\Alien\Models\Armor;

/**
 * @mixin Armor
 */
class ArmorResource extends JsonResource
{
    /**
     * @return array{
     *   air_supply: int,
     *   cost: int,
     *   description: MissingValue|string,
     *   id: string,
     *   modifiers: array<int, string>,
     *   name: string,
     *   page: int,
     *   rating: int,
     *   ruleset: string,
     *   weight: ?float,
     *   links: array{
     *     self: string
     *   },
     * }
     */
    public function toArray(Request $request): array
    {
        /** @var User */
        $user = $request->user();
        return [
            'air_supply' => $this->air_supply,
            'cost' => $this->cost,
            'description' => $this->when(
                $user->hasPermissionTo('view data'),
                $this->description,
            ),
            'id' => $this->id,
            'modifiers' => $this->modifiers,
            'name' => $this->name,
            'page' => $this->page,
            'rating' => $this->rating,
            'ruleset' => $this->ruleset,
            'weight' => $this->weight,
            'links' => [
                'self' => route('alien.armor.show', $this->id),
            ],
        ];
    }
}
