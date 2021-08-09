<?php

declare(strict_types=1);

namespace App\Http\Resources\Dnd5e;

use Illuminate\Http\Resources\Json\JsonResource;

class CharacterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * @param \Illuminate\Http\Request $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return \array_merge(parent::toArray($request), ['id' => $this->id]);
    }
}
