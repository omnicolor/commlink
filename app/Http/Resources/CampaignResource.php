<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Override;
use stdClass;

use function route;
use function sprintf;

/**
 * @mixin Campaign
 */
class CampaignResource extends JsonResource
{
    /**
     * @return array{
     *     characters: array<int, array{
     *         id: string,
     *         name: string,
     *         owner: array{id: int, name: string},
     *         links: array{
     *             self: string
     *         }
     *     }>,
     *     description: null|string,
     *     id: int,
     *     gm: array{id: int, name: string}|null,
     *     name: string,
     *     options: stdClass,
     *     players: array<int, array{id: int, name: string, status: string}>,
     *     registered_by: array{id: int|null, name: null|string},
     *     system: string,
     *     links: array{
     *         collection: string,
     *         self: string
     *     }
     * }
     */
    #[Override]
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
        foreach ($this->characters() as $character) {
            $characters[] = [
                'id' => $character->id,
                'name' => (string)$character,
                'owner' => [
                    'id' => $character->user()->id,
                    'name' => $character->user()->name,
                ],
                'links' => [
                    'self' => route(
                        sprintf(
                            '%s.characters.show',
                            $character->system,
                        ),
                        $character,
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
            'options' => (object)$this->options,
            'players' => $players,
            'registered_by' => [
                'id' => $this->registrant?->id,
                'name' => $this->registrant?->name,
            ],
            'system' => $this->system,
            'links' => [
                'collection' => route('campaigns.index'),
                'self' => route('campaigns.show', $this),
            ],
        ];
    }
}
