<?php

declare(strict_types=1);

namespace Modules\Capers\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Capers\Models\Virtue;

/**
 * @mixin Virtue
 */
class VirtueResource extends JsonResource
{
    /**
     * @return array{
     *     name: string,
     *     card: string,
     *     description?: string,
     *     id: string
     * }
     */
    public function toArray(Request $request): array
    {
        /** @var User */
        $user = $request->user();
        return [
            'name' => $this->name,
            'card' => $this->card,
            'description' => $this->when(
                $user->hasPermissionTo('view data'),
                $this->description,
            ),
            'id' => $this->id,
        ];
    }
}
