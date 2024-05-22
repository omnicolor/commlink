<?php

declare(strict_types=1);

namespace App\Http\Resources\Subversion;

use App\Models\Subversion\Origin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;

/**
 * @mixin Origin
 * @psalm-suppress UnusedClass
 */
class OriginResource extends JsonResource
{
    /**
     * @return array{
     *   description: MissingValue|string,
     *   id: string,
     *   name: string,
     *   page: int,
     *   ruleset: string,
     *   links: array{
     *     self: string,
     *   },
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
            'id' => $this->id,
            'name' => $this->name,
            'page' => $this->page,
            'ruleset' => $this->ruleset,
            'links' => [
                'self' => route('subversion.origins.show', $this->id),
            ],
        ];
    }
}
