<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Transformers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Shadowrun6e\Models\Metamagic;
use Override;

/**
 * @mixin Metamagic
 */
class MetamagicResource extends JsonResource
{
    /**
     * @return array{
     *     adept_only: bool,
     *     description?: string,
     *     id: string,
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
            'adept_only' => (bool)$this->adept_only,
            'description' => $this->when(
                $user->hasPermissionTo('view data'),
                $this->description,
            ),
            'id' => $this->id,
            'name' => $this->name,
            'page' => $this->page,
            'ruleset' => $this->ruleset,
            'links' => [
                'self' => route('shadowrun6e.metamagics.show', $this->id),
                'collection' => route('shadowrun6e.metamagics.index'),
            ],
        ];
    }
}
