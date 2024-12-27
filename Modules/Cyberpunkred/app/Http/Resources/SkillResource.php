<?php

declare(strict_types=1);

namespace Modules\Cyberpunkred\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Cyberpunkred\Models\Skill;

/**
 * @mixin Skill
 */
class SkillResource extends JsonResource
{
    /**
     * @return array{
     *     id: string,
     *     attribute: string,
     *     category: string,
     *     description?: string,
     *     examples?: string,
     *     name: string,
     *     page: int,
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
            'id' => $this->id,
            'attribute' => $this->attribute,
            'category' => $this->category,
            'description' => $this->when(
                $user->hasPermissionTo('view data'),
                $this->description,
            ),
            'examples' => $this->when(
                $user->hasPermissionTo('view data'),
                $this->examples,
            ),
            'name' => $this->name,
            'page' => $this->page,
            'links' => [
                'self' => route('cyberpunkred.armor.show', $this->id),
            ],
        ];
    }
}
