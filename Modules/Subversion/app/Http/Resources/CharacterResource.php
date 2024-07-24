<?php

declare(strict_types=1);

namespace Modules\Subversion\Http\Resources;

use App\Http\Resources\CampaignResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;
use Modules\Subversion\Models\Character;

use function array_values;

/**
 * @mixin Character
 * @psalm-suppress UnusedClass
 */
class CharacterResource extends JsonResource
{
    /**
     * @return array{
     *   name: string,
     *   agility: int,
     *   arts: int,
     *   awareness: int,
     *   background: BackgroundResource,
     *   brawn: int,
     *   campaign: CampaignResource|MissingValue,
     *   caste: CasteResource,
     *   charisma: int,
     *   grit_starting: int,
     *   id: string,
     *   ideology: IdeologyResource,
     *   lineage: LineageResource,
     *   links: array{
     *      self: string,
     *      owner: string,
     *   },
     *   origin: OriginResource,
     *   owner: string,
     *   skills: AnonymousResourceCollection,
     *   system: string,
     *   will: int,
     *   wit: int,
     * }
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
            'ideology' => new IdeologyResource($this->ideology),
            'lineage' => new LineageResource($this->lineage),
            'links' => [
                'self' => route('subversion.characters.show', $this),
                // @phpstan-ignore-next-line
                'owner' => route('users.show', $this->user()),
            ],
            'origin' => new OriginResource($this->origin),
            'owner' => $this->owner,
            'skills' => SkillResource::collection(array_values($this->skills)),
            'system' => $this->system,
            'will' => $this->will,
            'wit' => $this->wit,
        ];
    }
}
