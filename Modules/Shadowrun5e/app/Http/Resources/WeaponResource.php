<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Shadowrun5e\Models\Weapon;
use Override;

use function array_merge;
use function route;

/**
 * @mixin Weapon
 * @phpstan-type MeleeWeapon array{
 *     accuracy: string|null,
 *     armor_piercing: int,
 *     availability: string,
 *     class: string,
 *     cost: int|null,
 *     damage: string,
 *     description?: string,
 *     id: string,
 *     modifications: AnonymousResourceCollection,
 *     mounts: array<int, string>,
 *     name: string,
 *     page: int|null,
 *     reach: int|null,
 *     ruleset: string,
 *     skill: string,
 *     type: string,
 *     links: array{self: string}
 * }
 * @phpstan-type RangedWeapon array{
 *     accuracy: string|null,
 *     ammo_capacity: int|null,
 *     ammo_container: string|null,
 *     armor_piercing: int,
 *     availability: string,
 *     class: string,
 *     cost: int|null,
 *     damage: string,
 *     description?: string,
 *     firing_modes: array<int, string>,
 *     id: string,
 *     modifications: AnonymousResourceCollection,
 *     mounts: array<int, string>,
 *     name: string,
 *     page: int|null,
 *     range: string,
 *     ruleset: string,
 *     skill: string,
 *     type: string,
 *     links: array{self: string}
 *  }
 */
class WeaponResource extends JsonResource
{
    /**
     * @return MeleeWeapon|RangedWeapon
     */
    #[Override]
    public function toArray(Request $request): array
    {
        /** @var User */
        $user = $request->user();
        $weapon = [
            'accuracy' => $this->accuracy,
            'armor_piercing' => $this->armor_piercing ?? 0,
            'availability' => $this->availability,
            'class' => $this->class->name(),
            'cost' => $this->cost,
            'damage' => $this->damage,
            'description' => $this->when(
                $user->hasPermissionTo('view data'),
                $this->description,
            ),
            'id' => $this->id,
            'modifications' => WeaponModificationResource::collection((array)$this->modifications),
            'mounts' => (array)$this->accessories,
            'name' => $this->name,
            'page' => $this->page,
            'ruleset' => $this->ruleset,
            'skill' => $this->skill->id,
            'type' => $this->type,
            'links' => [
                'self' => route('shadowrun5e.weapons.show', $this->id),
            ],
        ];
        if ('firearm' === $this->type) {
            $modes = [];
            foreach ($this->modes as $mode) {
                $modes[] = $mode->value;
            }
            return array_merge(
                $weapon,
                [
                    'ammo_capacity' => $this->ammo_capacity,
                    'ammo_container' => $this->ammo_container,
                    'firing_modes' => $modes,
                    'range' => $this->range->range(),
                ],
            );
        }
        return array_merge(
            $weapon,
            [
                'reach' => $this->reach,
            ],
        );
    }
}
