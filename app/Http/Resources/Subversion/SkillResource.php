<?php

declare(strict_types=1);

namespace App\Http\Resources\Subversion;

use App\Models\Subversion\Skill;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;

/**
 * @mixin Skill
 * @psalm-suppress UnusedClass
 */
class SkillResource extends JsonResource
{
    /**
     * @return array{
     *   attributes: array<int, string>,
     *   description: MissingValue|string,
     *   id: string,
     *   name: string,
     *   page: int,
     *   rank: ?int,
     *   ruleset: string,
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
            'attributes' => $this->attributes,
            'description' => $this->when(
                $user->hasPermissionTo('view data'),
                $this->description,
            ),
            'id' => $this->id,
            'name' => $this->name,
            'page' => $this->page,
            'rank' => $this->rank,
            'ruleset' => $this->ruleset,
            'links' => [
                'self' => route('subversion.skills.show', $this->id),
            ],
        ];
    }
}
