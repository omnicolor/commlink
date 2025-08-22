<?php

declare(strict_types=1);

namespace Modules\Cyberpunkred\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Cyberpunkred\Models\Armor;
use Modules\Cyberpunkred\Models\Character;
use Modules\Cyberpunkred\Models\Weapon;
use Override;

use function route;
use function str_replace;

/**
 * @mixin Character
 */
class CharacterResource extends JsonResource
{
    /**
     * @return array{
     *     handle: string,
     *     id: string,
     *     body: int,
     *     cool: int,
     *     dexterity: int,
     *     empathy: int,
     *     intelligence: int,
     *     luck: int,
     *     movement: int,
     *     reflexes: int,
     *     technique: int,
     *     willpower: int,
     *     hit_points: int,
     *     hit_points_current: int,
     *     roles: array<int, array{role: string, rank: int, type?: int}>,
     *     skills: array<string, int>,
     *     skills_custom: array<int, array{type: string, name: string, level: int}>,
     *     lifepath: array<string, string>,
     *     weapons: AnonymousResourceCollection<Weapon>,
     *     armor: array{
     *         head: ArmorResource|null,
     *         body: ArmorResource|null,
     *         shield: ArmorResource|null,
     *         unworn: AnonymousResourceCollection<Armor>
     *     },
     *     campaign_id?: null,
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
        $weapons = [];
        foreach ($this->weapons ?? [] as $weapon) {
            $weapons[] = Weapon::build($weapon);
        }

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
            'hit_points' => $this->hit_points_max,
            'hit_points_current' => $this->hit_points_current,
            'roles' => $this->roles,
            'skills' => [],
            'skills_custom' => $this->skills_custom,
            'lifepath' => [],
            'weapons' => WeaponResource::collection($weapons),
            'armor' => [
                'head' => new ArmorResource($this->armor['head']),
                'body' => new ArmorResource($this->armor['body']),
                'shield' => new ArmorResource($this->armor['shield']),
                'unworn' => ArmorResource::collection($this->armor['unworn']),
            ],
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
