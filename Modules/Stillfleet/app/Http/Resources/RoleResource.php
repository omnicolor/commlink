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
 * @psalm-suppress UnusedClass
 */
class RoleResource extends JsonResource
{
    /**
     * @return array<string, array<int|string, string>|int|string>
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
            'advanced_power_lists' => $this->power_advanced,
            'marquee_power' => new PowerResource($this->power_marquee),
            'optional_powers' => array_values(
                (array)PowerResource::collection($this->powers_optional)
                    ->toArray($request)
            ),
            'other_powers' => array_values(
                (array)PowerResource::collection($this->powers_other)
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
