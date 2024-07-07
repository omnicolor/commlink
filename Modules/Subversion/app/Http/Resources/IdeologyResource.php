<?php

declare(strict_types=1);

namespace Modules\Subversion\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;
use Modules\Subversion\Models\Ideology;

/**
 * @mixin Ideology
 * @psalm-suppress UnusedClass
 */
class IdeologyResource extends JsonResource
{
    /**
     * @return array{
     *   description: MissingValue|string,
     *   id: string,
     *   name: string,
     *   page: int,
     *   ruleset: string,
     *   value: MissingValue|string,
     *   links: array{
     *     self: string,
     *   }
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
            'value' => $this->when(
                $user->hasPermissionTo('view data'),
                $this->value,
            ),
            'links' => [
                'self' => route('subversion.ideologies.show', $this->id),
            ],
        ];
    }
}
