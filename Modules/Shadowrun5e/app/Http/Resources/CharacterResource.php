<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Http\Resources;

use App\Http\Resources\JsonResource;
use Illuminate\Http\Request;

use function array_merge;

/**
 * @property string $id
 */
class CharacterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        $character = array_merge(
            (array)parent::toArray($request),
            ['id' => $this->id]
        );
        return $this->convertKeys($character);
    }
}
