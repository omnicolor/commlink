<?php

declare(strict_types=1);

namespace Modules\Capers\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Capers\Models\Gear;
use Override;

/**
 * @mixin Gear
 */
class GearResource extends JsonResource
{
    /**
     * @return array{
     *     name: string,
     *     cost: float,
     *     id: string,
     *     quantity?: int,
     *     type: string
     * }
     */
    #[Override]
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'cost' => $this->cost,
            'id' => $this->id,
            'quantity' => $this->when(
                0 !== $this->quantity,
                $this->quantity,
            ),
            'type' => $this->type,
        ];
    }
}
