<?php

declare(strict_types=1);

namespace Modules\Alien\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;
use Modules\Alien\Models\Career;

/**
 * @mixin Career
 * @psalm-suppress UnusedClass
 */
class CareerResource extends JsonResource
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
            'description' => $this->when(
                $user->hasPermissionTo('view data'),
                $this->description,
            ),
            'key_attribute' => $this->keyAttribute,
            'key_skills' => SkillResource::collection($this->keySkills),
            'id' => $this->id,
            'name' => $this->name,
            'page' => $this->page,
            'ruleset' => $this->ruleset,
            'talents' => TalentResource::collection($this->talents),
            'links' => [
                'self' => route('alien.careers.show', $this->id),
            ],
        ];
    }
}
