<?php

declare(strict_types=1);

namespace Modules\Stillfleet\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Stillfleet\Models\Role;

use function array_values;

/**
 * @mixin Role
 */
class RoleResource extends JsonResource
{
    /**
     * @return array{
     *     description?: string,
     *     grit: array<int, string>,
     *     id: string,
     *     name: string,
     *     page: int,
     *     advanced_power_lists: array<int, string>,
     *     marquee_power: PowerResource,
     *     optional_powers: array<int, PowerResource>,
     *     other_powers: array<int, PowerResource>,
     *     responsibilities: array<int, string>,
     *     ruleset: string,
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
            'grit' => $this->grit,
            'id' => $this->id,
            'name' => $this->name,
            'page' => $this->page,
            'advanced_power_lists' => $this->advanced_powers_lists,
            'marquee_power' => new PowerResource($this->marquee_power),
            'optional_powers' => array_values(
                (array)PowerResource::collection($this->optional_powers)
                    ->toArray($request)
            ),
            'other_powers' => array_values(
                (array)PowerResource::collection($this->other_powers)
                    ->toArray($request)
            ),
            'responsibilities' => $this->responsibilities,
            'ruleset' => $this->ruleset,
            'links' => [
                'self' => route('stillfleet.roles.show', $this->id),
            ],
        ];
    }
}
