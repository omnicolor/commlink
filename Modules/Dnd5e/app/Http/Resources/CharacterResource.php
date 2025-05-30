<?php

declare(strict_types=1);

namespace Modules\Dnd5e\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Dnd5e\Models\Character;
use Override;

use function route;

/**
 * @mixin Character
 */
class CharacterResource extends JsonResource
{
    /**
     * @return array{
     *     id: string,
     *     name: string,
     *     campaign_id?: int,
     *     owner: array{
     *         id: int,
     *         name: string
     *     },
     *     system: string,
     *     links: array{
     *         self: string,
     *         campaign?: string
     *     }
     * }
     */
    #[Override]
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'campaign_id' => $this->when(
                null !== $this->campaign_id,
                $this->campaign_id,
            ),
            'owner' => [
                'id' => $this->user()->id,
                'name' => $this->user()->name,
            ],
            'system' => $this->system,
            'links' => [
                'self' => route('dnd5e.characters.show', $this->id),
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
