<?php

declare(strict_types=1);

namespace Modules\Alien\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;
use Modules\Alien\Models\Talent;

/**
 * @mixin Talent
 * @psalm-suppress UnusedClass
 */
class TalentResource extends JsonResource
{
    /**
     * @psalm-suppress InvalidReturnStatement
     * @psalm-suppress InvalidReturnType
     * @return array<string, array<string, MissingValue|array<string, string>|int|mixed|string>>
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
