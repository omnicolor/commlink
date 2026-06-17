<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Shadowrun6e\Models\Spirit;
use Override;

use function route;

/**
 * @mixin Spirit
 */
class SpiritResource extends JsonResource
{
    /**
     * @return array{
     *     agility: int|string,
     *     body: int|string,
     *     charisma: int|string,
     *     description?: string,
     *     force?: int,
     *     id: string,
     *     intuition: int|string,
     *     logic: int|string,
     *     name: string,
     *     page: int,
     *     reaction: int|string,
     *     ruleset: string,
     *     strength: int|string,
     *     willpower: int|string,
     *     links: array{
     *         self: string,
     *         collection: string
     *     }
     * }
     */
    #[Override]
    public function toArray(Request $request): array
    {
        return [
            'agility' => $this->agility,
            'body' => $this->body,
            'charisma' => $this->charisma,
            'force' => $this->whenHas('force'),
            'id' => $this->id,
            'intuition' => $this->intuition,
            'logic' => $this->logic,
            'name' => $this->name,
            'page' => $this->page,
            'reaction' => $this->reaction,
            'ruleset' => $this->ruleset,
            'strength' => $this->strength,
            'willpower' => $this->willpower,
            'links' => [
                'self' => $this->whenHas(
                    'force',
                    route(
                        'shadowrun6e.spirits.show',
                        ['spirit' => $this->id, 'force' => $this->force],
                    ),
                    route('shadowrun6e.spirits.show', $this->id),
                ),
                'collection' => route('shadowrun6e.spirits.index'),
            ],
        ];
    }
}
