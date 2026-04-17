<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Transformers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Shadowrun6e\Models\Quality;
use Override;

/**
 * @mixin Quality
 */
class QualityResource extends JsonResource
{
    /**
     * @return array{
     *     description?: string,
     *     effects: array<string, mixed>|null,
     *     id: string,
     *     karma_cost: int,
     *     level: int|string|null,
     *     name: string,
     *     page: int,
     *     ruleset: string,
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
            'description' => $this->when(
                $user->hasPermissionTo('view data'),
                $this->description,
            ),
            'effects' => $this->effects,
            'id' => $this->id,
            'karma_cost' => $this->karma_cost,
            'level' => $this->level,
            'name' => $this->name,
            'page' => $this->page,
            'ruleset' => $this->ruleset,
            'links' => [
                'self' => route('shadowrun6e.qualities.show', $this->id),
                'collection' => route('shadowrun6e.qualities.index'),
            ],
        ];
    }
}
