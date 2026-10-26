<?php

declare(strict_types=1);

namespace Modules\Battletech\Transformers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Battletech\Enums\Attribute;
use Modules\Battletech\Models\Skill;
use Modules\Battletech\ValueObjects\Attributes;
use Override;

/**
 * @mixin Skill
 */
class SkillResource extends JsonResource
{
    /**
     * @return array{
     *     action_rating: string,
     *     attributes: array<int, Attribute>,
     *     description?: string,
     *     id: string,
     *     name: string,
     *     page: int,
     *     quote: string,
     *     ruleset: string,
     *     sub_description?: string|null,
     *     sub_name: string|null,
     *     target_number: int,
     *     training_rating: string,
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
            'action_rating' => $this->action_rating->value,
            'attributes' => $this->attributes,
            'description' => $this->when(
                $user->hasPermissionTo('view data'),
                $this->description,
            ),
            'id' => $this->id,
            'name' => $this->name,
            'page' => $this->page,
            'quote' => $this->quote,
            'ruleset' => $this->ruleset,
            'sub_description' => $this->when(
                $user->hasPermissionTo('view data'),
                $this->sub_description,
            ),
            'sub_name' => $this->sub_name,
            'target_number' => $this->target_number,
            'training_rating' => $this->training_rating->value,
            'links' => [
                'self' => route('battletech.skills.show', $this->id),
            ],
        ];
    }
}
