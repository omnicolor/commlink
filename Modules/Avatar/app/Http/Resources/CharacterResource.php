<?php

declare(strict_types=1);

namespace Modules\Avatar\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;
use Modules\Avatar\Models\Character;

/**
 * @mixin Character
 */
class CharacterResource extends JsonResource
{
    /**
     * @return array{
     *   name: string,
     *   appearance: string,
     *   background: string,
     *   creativity: int,
     *   fatigue: int,
     *   focus: int,
     *   harmony: int,
     *   history: ?string,
     *   passion: int,
     *   playbook: PlaybookResource,
     *   campaign_id: MissingValue|string,
     *   id: string,
     *   owner: array{
     *     id: int,
     *     name: string
     *   },
     *   system: string,
     *   links: array{
     *     campaign: MissingValue|string,
     *     playbook: string,
     *     self: string
     *   }
     * }
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'appearance' => $this->appearance,
            'background' => ucfirst($this->background->value),
            'creativity' => $this->creativity,
            //'era' => $this->era,
            'fatigue' => $this->fatigue,
            'focus' => $this->focus,
            'harmony' => $this->harmony,
            'history' => $this->history,
            'passion' => $this->passion,
            'playbook' => new PlaybookResource($this->playbook),
            'campaign_id' => $this->when(
                null !== $this->campaign_id,
                $this->campaign_id
            ),
            'id' => $this->id,
            'owner' => [
                // @phpstan-ignore-next-line
                'id' => $this->user()->id,
                // @phpstan-ignore-next-line
                'name' => $this->user()->name,
            ],
            'system' => $this->system,
            'links' => [
                'campaign' => $this->when(
                    null !== $this->campaign_id,
                    null !== $this->campaign_id
                        ? route('campaigns.show', $this->campaign_id)
                        : null,
                ),
                'playbook' => route('avatar.playbooks.show', $this->playbook->id),
                'self' => route('avatar.characters.show', $this->id),
            ],
        ];
    }
}
