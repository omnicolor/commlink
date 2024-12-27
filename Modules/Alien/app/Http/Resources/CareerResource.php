<?php

declare(strict_types=1);

namespace Modules\Alien\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Alien\Models\Career;
use Modules\Alien\Models\Skill;
use Modules\Alien\Models\Talent;

/**
 * @mixin Career
 */
class CareerResource extends JsonResource
{
    /**
     * @return array{
     *     description?: string,
     *     key_attribute: string,
     *     key_skills: AnonymousResourceCollection<Skill>,
     *     id: string,
     *     name: string,
     *     page: int,
     *     ruleset: string,
     *     talents: AnonymousResourceCollection<Talent>,
     *     links: array{
     *         self: string
     *     }
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
