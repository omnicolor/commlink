<?php

declare(strict_types=1);

namespace Modules\Transformers\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Transformers\Models\Weapon;

/**
 * @codeCoverageIgnore Not yet used.
 * @mixin Weapon
 */
class WeaponResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var User */
        $user = $request->user();
        return [
            'name' => $this->name,
            'class' => $this->class,
            // @phpstan-ignore-next-line
            'cost' => $this->cost(),
            'damage' => $this->damage,
            'explanation' => $this->when(
                $user->hasPermissionTo('view data'),
                $this->explanation,
            ),
            'id' => $this->id,
        ];
    }
}
