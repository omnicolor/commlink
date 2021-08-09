<?php

declare(strict_types=1);

namespace App\Http\Resources\Expanse;

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
        $array = \array_merge(
            (array)parent::toArray($request),
            ['id' => $this->id]
        );
        unset(
            $array['_id'],
            $array['abilities'],
        );
        return $array;
    }
}
