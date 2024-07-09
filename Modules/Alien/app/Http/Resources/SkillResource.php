<?php

declare(strict_types=1);

namespace Modules\Alien\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;
use Modules\Alien\Models\Skill;

/**
 * @mixin Skill
 * @psalm-suppress UnusedClass
 */
class SkillResource extends JsonResource
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
            'attribute' => $this->attribute,
            'description' => $this->when(
                $user->hasPermissionTo('view data'),
                $this->description,
            ),
            'id' => $this->id,
            'name' => $this->name,
            'page' => $this->page,
            'ruleset' => $this->ruleset,
            'stunts' => $this->when(
                0 !== count($this->stunts),
                $this->stunts,
            ),
            'links' => [
                'self' => route('alien.skills.show', $this->id),
            ],
        ];
    }
}
