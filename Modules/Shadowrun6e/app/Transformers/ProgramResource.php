<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Transformers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Shadowrun6e\Models\Program;
use Override;

/**
 * @mixin Program
 */
class ProgramResource extends JsonResource
{
    /**
     * @return array{
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
            'description' => $this->when(
                $user->hasPermissionTo('view data'),
                $this->description,
            ),
            'id' => $this->id,
            'name' => $this->name,
            'page' => $this->page,
            'ruleset' => $this->ruleset,
            'links' => [
                'self' => route('shadowrun6e.programs.show', $this->id),
                'collection' => route('shadowrun6e.programs.index'),
            ],
        ];
    }
}
