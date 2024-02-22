<?php

declare(strict_types=1);

namespace App\Http\Resources\Expanse;

use App\Models\Expanse\Character;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Character
 */
class CharacterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $focuses = [];
        // @phpstan-ignore-next-line
        foreach ($this->getFocuses() as $focus) {
            $focuses[] = [
                'attribute' => $focus->attribute,
                'description' => $focus->description,
                'id' => $focus->id,
                'name' => $focus->name,
                'page' => $focus->page,
                'level' => $focus->level,
                'links' => [
                    'self' => route('expanse.focuses.show', ['focus' => $focus->id]),
                ],
            ];
        }

        $background = (array)$this->background;
        unset($background['benefits']);
        $background['links'] = [
            'self' => route(
                'expanse.backgrounds.show',
                ['background' => $background['id']],
            ),
        ];

        $campaign = null;
        if (null !== $this->campaign_id) {
            $campaign = [
                'id' => $this->campaign_id,
                // @phpstan-ignore-next-line
                'name' => $this->campaign()?->name,
                'links' => [
                    'self' => route(
                        'campaigns.show',
                        // @phpstan-ignore-next-line
                        ['campaign' => $this->campaign()]
                    ),
                ],
            ];
        }
        return [
            'id' => $this->_id,
            'name' => $this->name,
            'accuracy' => $this->accuracy,
            'communication' => $this->communication,
            'constitution' => $this->constitution,
            'dexterity' => $this->dexterity,
            'fighting' => $this->fighting,
            'intelligence' => $this->intelligence,
            'perception' => $this->perception,
            'strength' => $this->strength,
            'toughness' => $this->toughness,
            'willpower' => $this->willpower,
            'age' => $this->age,
            'level' => $this->level,
            'speed' => $this->speed,
            'focuses' => $focuses,
            //'abilities' => $this->abilities,
            'background' => $background,
            'downfall' => $this->downfall,
            'drive' => $this->drive,
            'origin' => $this->origin,
            'profession' => $this->profession,
            'quality' => $this->quality,
            'social_class' => $this->socialClass,
            'talents' => $this->talents,
            'campaign' => $this->when(null !== $campaign, $campaign),
            'owner' => $this->owner,
            'system' => $this->system,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
