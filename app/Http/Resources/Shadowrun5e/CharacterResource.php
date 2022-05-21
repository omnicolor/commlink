<?php

declare(strict_types=1);

namespace App\Http\Resources\Shadowrun5e;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property string $id
 */
class CharacterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * @param \Illuminate\Http\Request $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return \array_merge(
            (array)parent::toArray($request),
            ['id' => $this->id]
        );
    }
}
