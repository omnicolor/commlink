<?php

declare(strict_types=1);

namespace Modules\Avatar\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;
use Modules\Avatar\Models\Status;
use Override;

use function route;

/**
 * @mixin Status
 */
class StatusResource extends JsonResource
{
    /**
     * @return array{
     *   description: MissingValue|string,
     *   effect: string,
     *   id: string,
     *   name: string,
     *   page: int,
     *   ruleset: string,
     *   short_description: string,
     *   links: array{
     *     self: string
     *   }
     * }
     */
    #[Override]
    public function toArray(Request $request): array
    {
        /** @var User */
        $user = $request->user();
        return [
            'description' => $this->when(
                $user->hasPermissionTo('view data'),
                $this->description,
            ),
            'effect' => $this->effect,
            'id' => $this->id,
            'name' => $this->name,
            'page' => $this->page,
            'ruleset' => $this->ruleset,
            'short_description' => $this->short_description,
            'links' => [
                'self' => route('avatar.statuses.show', $this->id),
            ],
        ];
    }
}
