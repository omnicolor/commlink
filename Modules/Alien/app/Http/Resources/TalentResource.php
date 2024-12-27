<?php

declare(strict_types=1);

namespace Modules\Alien\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Alien\Models\Talent;

/**
 * @mixin Talent
 */
class TalentResource extends JsonResource
{
    /**
     * @return array{
     *     career: null|string,
     *     description?: string,
     *     id: string,
     *     name: string,
     *     page: int,
     *     ruleset: string,
     *     links: array{
     *         self: string,
     *         career: string
     *     }
     * }
     */
    public function toArray(Request $request): array
    {
        /** @var User */
        $user = $request->user();
        return [
            'career' => $this->career,
            'description' => $this->when(
                $user->hasPermissionTo('view data'),
                $this->description,
            ),
            'id' => $this->id,
            'name' => $this->name,
            'page' => $this->page,
            'ruleset' => $this->ruleset,
            'links' => [
                'self' => route('alien.talents.show', $this->id),
                'career' => $this->when(
                    null !== $this->career,
                    route('alien.careers.show', $this->career ?? ''),
                ),
            ],
        ];
    }
}
