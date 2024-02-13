<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Campaign;
use App\Models\Character;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Campaign
 */
class CampaignResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $gm = null;
        if (null !== $this->gamemaster) {
            $gm = [
                'id' => $this->gamemaster->id,
                'name' => $this->gamemaster->name,
            ];
        }
        $players = [];
        if (0 !== $this->users->count()) {
            foreach ($this->users as $player) {
                if (
                    'accepted' === $player->pivot->status
                    || 'invited' === $player->pivot->status
                ) {
                    $players[] = [
                        'id' => $player->id,
                        'name' => $player->name,
                        'status' => $player->pivot->status,
                    ];
                }
            }
        }

        $characters = [];
        /**
         * @var Character
         * @phpstan-ignore-next-line
         */
        foreach ($this->characters() as $character) {
            $characters[] = [
                'id' => $character->id,
                'name' => (string)$character,
                'owner' => [
                    'id' => $character->user()->id,
                    'name' => $character->user()->name,
                ],
                'links' => [
                    'self' => sprintf(
                        '/characters/%s/%s',
                        $character->system,
                        $character->id,
                    ),
                ],
            ];
        }

        return [
            'characters' => $characters,
            'description' => $this->description,
            'id' => $this->id,
            'gm' => $gm,
            'name' => $this->name,
            'options' => $this->options,
            'players' => $players,
            'registered_by' => [
                'id' => $this->registrant?->id,
                'name' => $this->registrant?->name,
            ],
            'system' => $this->system,
            'links' => [
                'root' => '/',
                'collection' => '/campaigns',
                'self' => sprintf('/campaigns/%d', $this->id),
            ],
        ];
    }
}
