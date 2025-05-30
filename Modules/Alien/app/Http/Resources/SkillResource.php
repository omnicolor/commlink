<?php

declare(strict_types=1);

namespace Modules\Alien\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Alien\Models\Skill;
use Override;

use function count;
use function route;

/**
 * @mixin Skill
 */
class SkillResource extends JsonResource
{
    /**
     * @return array{
     *     attribute: string,
     *     description?: string,
     *     id: string,
     *     name: string,
     *     page: int,
     *     ruleset: string,
     *     stunts?: array<int, string>,
     *     links: array{
     *         self: string
     *     }
     * }
     */
    #[Override]
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
