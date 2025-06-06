<?php

declare(strict_types=1);

namespace Modules\Expanse\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Expanse\Models\Character;
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
     *     accuracy: int,
     *     communication: int,
     *     constitution: int,
     *     dexterity: int,
     *     fighting: int,
     *     intelligence: int,
     *     perception: int,
     *     strength: int,
     *     toughness: int,
     *     willpower: int,
     *     age: int|null,
     *     level: int,
     *     speed: int
     * }
     */
    #[Override]
    public function toArray(Request $request): array
    {
        $focuses = [];
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
                'name' => $this->campaign()?->name,
                'links' => [
                    'self' => route(
                        'campaigns.show',
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
