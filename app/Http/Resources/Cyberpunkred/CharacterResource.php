<?php

declare(strict_types=1);

namespace App\Http\Resources\Cyberpunkred;

use App\Models\Cyberpunkred\Character;
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
        $character = [
            'handle' => $this->handle,
            'id' => $this->id,
            'body' => $this->body,
            'cool' => $this->cool,
            'dexterity' => $this->dexterity,
            'empathy' => $this->empathy,
            'intelligence' => $this->intelligence,
            'luck' => $this->luck,
            'movement' => $this->movement,
            'reflexes' => $this->reflexes,
            'technique' => $this->technique,
            'willpower' => $this->willpower,
            // @phpstan-ignore-next-line TODO Fix hit points
            'hit_points' => $this->hit_points,
            // @phpstan-ignore-next-line TODO Fix hit points
            'hit_points_current' => $this->hit_points_current,
            'roles' => $this->roles,
            'skills' => [],
            'skills_custom' => $this->skills_custom,
            'lifepath' => [],
            'weapons' => $this->weapons,
            'armor' => $this->armor,
            'campaign_id' => $this->when(
                null !== $this->campaign_id,
                $this->campaign_id,
            ),
            'owner' => $this->owner,
            'system' => $this->system,
            'links' => [
                'self' => route('cyberpunkred.characters.show', ['character' => $this->id]),
                'campaign' => $this->when(
                    null !== $this->campaign_id,
                    null !== $this->campaign_id
                        ? route('campaigns.show', ['campaign' => $this->campaign_id])
                        : null,
                ),
            ],
        ];
        foreach ($this->skills ?? [] as $skill => $rating) {
            $skill = str_replace('-', '_', $skill);
            $character['skills'][$skill] = $rating;
        }
        foreach ($this->lifepath ?? [] as $path => $description) {
            $path = str_replace('-', '_', $path);
            $character['lifepath'][$path] = $description;
        }
        return $character;
    }
}
