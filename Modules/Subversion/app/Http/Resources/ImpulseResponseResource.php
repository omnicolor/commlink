<?php

declare(strict_types=1);

namespace Modules\Subversion\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;
use Modules\Subversion\Models\ImpulseResponse;
use Override;
use stdClass;

/**
 * @mixin ImpulseResponse
 */
class ImpulseResponseResource extends JsonResource
{
    /**
     * @return array{
     *   description: MissingValue|string,
     *   effects: array<string, int>|stdClass,
     *   id: string,
     *   name: string,
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
            'effects' => (object)$this->effects,
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
}
