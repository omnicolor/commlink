<?php

declare(strict_types=1);

namespace Modules\Blistercritters\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Blistercritters\Models\Character;

use function route;

/**
 * @mixin Character
 */
class CharacterResource extends JsonResource
{
    /**
     * @return array<string, array<string, int|string>|int|string>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'instinct' => $this->instinct,
            'noggin' => $this->noggin,
            'scrap' => $this->scrap,
            'scurry' => $this->scurry,
            'vibe' => $this->vibe,
            'id' => $this->id,
            'campaign_id' => $this->campaign_id,
            'owner' => [
                // @phpstan-ignore-next-line
                'id' => $this->user()->id,
                // @phpstan-ignore-next-line
                'name' => $this->user()->name,
            ],
            'system' => $this->system,
            'links' => [
                'self' => route('blistercritters.characters.show', $this->id),
                'campaign' => $this->when(
                    null !== $this->campaign_id,
                    null !== $this->campaign_id
                        ? route('campaigns.show', $this->campaign_id)
                        : null,
                ),
            ],
        ];
    }
}
