<?php

declare(strict_types=1);

namespace Modules\Avatar\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;
use Modules\Avatar\Models\Move;

/**
 * @mixin Move
 */
class MoveResource extends JsonResource
{
    /**
     * @return array{
     *   description: MissingValue|string,
     *   id: string,
     *   name: string,
     *   page: int,
     *   playbook: ?string,
     *   ruleset: string,
     *   links: array{
     *     playbook: MissingValue|string,
     *     self: string
     *   }
     * }
     */
    public function toArray(Request $request): array
    {
        /** @var User */
        $user = $request->user();
        return [
            'description' => $this->when(
                $user->hasPermissionTo('view data'),
                $this->description,
            ),
            'id' => $this->id,
            'name' => $this->name,
            'page' => $this->page,
            'playbook' => $this->when(
                null !== $this->playbook,
                $this->playbook?->id,
            ),
            'ruleset' => $this->ruleset,
            'links' => [
                'self' => route('avatar.moves.show', $this->id),
                'playbook' => $this->when(
                    null !== $this->playbook,
                    route('avatar.playbooks.show', $this->playbook->id ?? ''),
                ),
            ],
        ];
    }
}
