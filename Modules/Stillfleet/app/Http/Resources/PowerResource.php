<?php

declare(strict_types=1);

namespace Modules\Stillfleet\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Stillfleet\Models\Power;

/**
 * @mixin Power
 */
class PowerResource extends JsonResource
{
    /**
     * @return array{
     *     advanced_list?: string,
     *     description?: string,
     *     id: string,
     *     name: string,
     *     page: int,
     *     ruleset: string,
     *     type: string,
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
            'advanced_list' => $this->when(
                null !== $this->advanced_list,
                $this->advanced_list,
            ),
            'description' => $this->when(
                $user->hasPermissionTo('view data'),
                $this->description,
            ),
            'id' => $this->id,
            'name' => $this->name,
            'page' => $this->page,
            'ruleset' => $this->ruleset,
            'type' => $this->type,
            'links' => [
                'self' => route('stillfleet.powers.show', $this->id),
            ],
        ];
    }
}
