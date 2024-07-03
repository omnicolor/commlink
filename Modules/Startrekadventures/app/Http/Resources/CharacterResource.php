<?php

declare(strict_types=1);

namespace Modules\Startrekadventures\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Startrekadventures\Models\Character;

/**
 * @mixin Character
 */
class CharacterResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var User */
        $user = $request->user();
        return [
            'name' => $this->name,
            'assignment' => $this->assignment,
            'disciplines' => [
                'command' => $this->disciplines->command,
                'conn' => $this->disciplines->conn,
                'engineering' => $this->disciplines->engineering,
                'medicine' => $this->disciplines->medicine,
                'science' => $this->disciplines->science,
                'security' => $this->disciplines->security,
            ],
            'environment' => $this->environment,
            //'equipment' => $this->equipment,
            'focuses' => $this->focuses,
            //'injuries' => $this->injuries,
            'rank' => $this->rank,
            'species' => [
                'name' => $this->species->name,
                'description' => $this->when(
                    $user->hasPermissionTo('view data'),
                    $this->species->description,
                ),
            ],
            'stats' => $this->stats,
            'talents' => TalentResource::collection((array)$this->talents),
            'trait' => $this->trait,
            'upbringing' => $this->upbringing,
            'values' => $this->values,
            //'weapons' => [],
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
                'self' => route('startrekadventures.characters.show', $this->id),
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
