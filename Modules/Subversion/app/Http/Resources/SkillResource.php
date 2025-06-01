<?php

declare(strict_types=1);

namespace Modules\Subversion\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;
use Modules\Subversion\Models\Skill;
use Override;

use function route;

/**
 * @mixin Skill
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
    #[Override]
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
