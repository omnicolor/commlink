<?php

declare(strict_types=1);

namespace Modules\Cyberpunkred\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Cyberpunkred\Models\Armor;

/**
 * @mixin Armor
 */
class ArmorResource extends JsonResource
{
    /**
     * @return array{
     *     id: string,
     *     type: string,
     *     cost_category: string,
     *     description?: string,
     *     page: int,
     *     penalty: int,
     *     ruleset: string,
     *     stopping_power: int,
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
            'type' => $this->type,
            'cost_category' => $this->cost_category->value,
            'description' => $this->when(
                $user->hasPermissionTo('view data'),
                $this->description,
            ),
            'page' => $this->page,
            'penalty' => $this->penalty,
            'ruleset' => $this->ruleset,
            'stopping_power' => $this->stopping_power,
            'links' => [
                'self' => route('cyberpunkred.armor.show', $this->id),
            ],
        ];
    }
}
