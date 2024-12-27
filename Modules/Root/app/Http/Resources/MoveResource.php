<?php

declare(strict_types=1);

namespace Modules\Root\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;
use Modules\Root\Models\Move;

/**
 * @mixin Move
 */
class MoveResource extends JsonResource
{
    /**
     * @return array{
     *     description: MissingValue|string,
     *     effects: null|object,
     *     id: string,
     *     name: string,
     *     weapon_move: bool,
     *     links: array{
     *         self: string
     *     }
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
            'effects' => $this->effects,
            'id' => $this->id,
            'name' => $this->name,
            'weapon_move' => $this->weapon_move,
            'links' => [
                'self' => route('root.moves.show', $this->id),
            ],
        ];
    }
}
