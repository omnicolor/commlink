<?php

declare(strict_types=1);

namespace App\Http\Resources\Subversion;

use App\Http\Resources\CampaignResource;
use App\Models\Subversion\Character;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Character
 * @psalm-suppress UnusedClass
 */
class CharacterResource extends JsonResource
{
    /**
     * @psalm-suppress InvalidArgument
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'agility' => $this->agility,
            'arts' => $this->arts,
            'awareness' => $this->awareness,
            'background' => new BackgroundResource($this->background),
            'brawn' => $this->brawn,
            'campaign' => $this->when(
                null !== $this->campaign_id,
                // @phpstan-ignore-next-line
                new CampaignResource($this->campaign()),
            ),
            'caste' => new CasteResource($this->caste),
            'charisma' => $this->charisma,
            'grit_starting' => $this->grit_starting,
            'id' => $this->_id,
            'lineage' => new LineageResource($this->lineage),
            'links' => [
                'self' => route('subversion.characters.show', $this),
                // @phpstan-ignore-next-line
                'owner' => route('users.show', $this->user()),
            ],
            'origin' => new OriginResource($this->origin),
            'owner' => $this->owner,
            'system' => $this->system,
            'will' => $this->will,
            'wit' => $this->wit,
        ];
    }
}
