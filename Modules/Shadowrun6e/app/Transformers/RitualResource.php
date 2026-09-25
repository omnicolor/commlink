<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Transformers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Shadowrun6e\Models\Ritual;
use Override;

/**
 * @mixin Ritual
 */
class RitualResource extends JsonResource
{
    /**
     * @return array{
     *     anchored: bool,
     *     description?: string,
     *     id: string,
     *     material_link: bool,
     *     minion: bool,
     *     name: string,
     *     page: int,
     *     ruleset: string,
     *     spell: bool,
     *     spotter: bool,
     *     threshold: int,
     *     links: array{
     *         self: string,
     *         collection: string
     *     }
     * }
     */
    #[Override]
    public function toArray(Request $request): array
    {
        /** @var User $user */
        $user = $request->user();
        return [
            'anchored' => $this->anchored,
            'description' => $this->when(
                $user->hasPermissionTo('view data'),
                $this->description,
            ),
            'id' => $this->id,
            'material_link' => $this->material_link,
            'minion' => $this->minion,
            'name' => $this->name,
            'page' => $this->page,
            'ruleset' => $this->ruleset,
            'spell' => $this->spell,
            'spotter' => $this->spotter,
            'threshold' => $this->threshold,
            'links' => [
                'self' => route('shadowrun6e.rituals.show', $this->id),
                'collection' => route('shadowrun6e.rituals.index'),
            ],
        ];
    }
}
