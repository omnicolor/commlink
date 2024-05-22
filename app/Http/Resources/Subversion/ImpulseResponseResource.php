<?php

declare(strict_types=1);

namespace App\Http\Resources\Subversion;

use App\Models\Subversion\ImpulseResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;

/**
 * @mixin ImpulseResponse
 * @psalm-suppress UnusedClass
 */
class ImpulseResponseResource extends JsonResource
{
    /**
     * @return array{
     *   description: MissingValue|string,
     *   effects: array<string, int>,
     *   id: string,
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
            'effects' => $this->effects,
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
}
