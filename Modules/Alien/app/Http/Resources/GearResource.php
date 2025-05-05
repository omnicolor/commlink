<?php

declare(strict_types=1);

namespace Modules\Alien\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Alien\Models\Gear;
use stdClass;

/**
 * @mixin Gear
 */
class GearResource extends JsonResource
{
    /**
     * @return array{
     *     category: string,
     *     cost: ?int,
     *     description?: string,
     *     effects: stdClass,
     *     effects_text: string,
     *     name: string,
     *     page: integer,
     *     quantity: integer,
     *     ruleset: string,
     *     weight: ?float
     * }
     */
    public function toArray(Request $request): array
    {
        /** @var User */
        $user = $request->user();
        return [
            'category' => $this->category,
            'cost' => $this->cost,
            'description' => $this->when(
                $user->hasPermissionTo('view data'),
                $this->description,
            ),
            'effects' => (object)$this->effects,
            'effects_text' => $this->effects_text,
            'name' => $this->name,
            'page' => $this->page,
            'quantity' => $this->quantity,
            'ruleset' => $this->ruleset,
            'weight' => $this->weight,
        ];
    }
}
