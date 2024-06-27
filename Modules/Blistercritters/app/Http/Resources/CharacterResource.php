<?php

declare(strict_types=1);

namespace Modules\Blistercritters\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Blistercritters\Models\Character;

class CharacterResource extends JsonResource
{
    /**
     * @mixin Character
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'instinct' => $this->instinct,
            'noggin' => $this->noggin,
            'scrap' => $this->scrap,
            'scurry' => $this->scurry,
            'vibe' => $this->vibe,
            'owner' => [
                'id' => $this->user()->id,
                'name' => $this->user()->name,
            ],
            'system' => $this->system,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'links' => [
                'self' => sprintf('/api/blistercritters/characters/%s', $this->id),
            ],
        ];
    }
}
