<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Shadowrun5e\Models\WeaponModification;
use Override;
use stdClass;

/**
 * @mixin WeaponModification
 */
class WeaponModificationResource extends JsonResource
{
    /**
     * @return array{
     *     availability: string,
     *     cost?: int,
     *     cost_modifier?: int|string,
     *     description?: string,
     *     effects: array<string, int>|stdClass,
     *     id: string,
     *     mount: array<int, string>,
     *     name: string,
     *     page: int|null,
     *     ruleset: string,
     *     type: string,
     *     wireless_effects: array<string, int>|stdClass
     * }
     */
    #[Override]
    public function toArray(Request $request): array
    {
        /** @var User */
        $user = $request->user();
        return [
            'availability' => $this->availability,
            'cost' => $this->when(
                null !== $this->cost,
                $this->cost,
            ),
            'cost_modifier' => $this->when(
                null !== $this->costModifier,
                $this->costModifier,
            ),
            'description' => $this->when(
                $user->hasPermissionTo('view data'),
                $this->description,
            ),
            'effects' => (object)$this->effects,
            'id' => $this->id,
            'mount' => $this->mount,
            'name' => $this->name,
            'page' => $this->page,
            'ruleset' => $this->ruleset,
            'type' => $this->type,
            'wireless_effects' => (object)$this->wirelessEffects,
        ];
    }
}
