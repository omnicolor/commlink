<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Transformers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Shadowrun6e\Models\ActiveSkill;
use Modules\Shadowrun6e\ValueObjects\SkillSpecialization;
use Override;

use function count;

/**
 * @mixin ActiveSkill
 */
class SkillResource extends JsonResource
{
    /**
     * @return array{
     *     attribute: string,
     *     attributes_secondary: array<int, string>|null,
     *     description?: string,
     *     example_specializations: array<int, string>,
     *     id: string,
     *     level?: int,
     *     name: string,
     *     page: int,
     *     specializations?: array<int, SkillSpecialization>,
     *     untrained: bool
     * }
     */
    #[Override]
    public function toArray(Request $request): array
    {
        /** @var User $user */
        $user = $request->user();
        return [
            'attribute' => $this->attribute,
            'attributes_secondary' => $this->attributes_secondary,
            'description' => $this->when(
                $user->hasPermissionTo('view data'),
                $this->description,
            ),
            'example_specializations' => $this->example_specializations,
            'id' => $this->id,
            'level' => $this->when(
                0 !== $this->level,
                $this->level,
            ),
            'name' => $this->name,
            'page' => $this->page,
            'specializations' => $this->when(
                0 !== count($this->specializations),
                $this->specializations,
            ),
            'untrained' => $this->untrained,
            'links' => [
                'self' => route('shadowrun6e.skills.show', $this->id),
                'collection' => route('shadowrun6e.skills.index'),
            ],
        ];
    }
}
