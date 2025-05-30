<?php

declare(strict_types=1);

namespace Modules\Avatar\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Avatar\Models\Character;
use Modules\Avatar\ValueObjects\Attribute;
use Override;

use function route;
use function ucfirst;

/**
 * @mixin Character
 */
class CharacterResource extends JsonResource
{
    /**
     * @return array{
     *     name: string,
     *     appearance: string,
     *     background: string,
     *     creativity: Attribute,
     *     fatigue: int,
     *     focus: Attribute,
     *     harmony: Attribute,
     *     history: string,
     *     passion: Attribute,
     *     campaign_id?: int,
     *     id: string,
     *     owner: array{
     *         id: int,
     *         name: string
     *     },
     *     system: string,
     *     links: array{
     *         self: string,
     *         campaign?: string,
     *         playbook: string
     *     }
     * }
     */
    #[Override]
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
                'id' => $this->user()->id,
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
