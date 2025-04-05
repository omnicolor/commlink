<?php

declare(strict_types=1);

namespace Modules\Subversion\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;
use Modules\Subversion\Models\ImpulseDowntime;
use stdClass;

/**
 * @mixin ImpulseDowntime
 */
class ImpulseDowntimeResource extends JsonResource
{
    /**
     * @return array{
     *   description: MissingValue|string,
     *   effects: array<string, int>|stdClass,
     *   name: string,
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
            'effects' => (object)$this->effects,
            'name' => $this->name,
        ];
    }
}
