<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Transformers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Shadowrun6e\Enums\DamageType;
use Modules\Shadowrun6e\Enums\SpellCategory;
use Modules\Shadowrun6e\Enums\SpellDuration;
use Modules\Shadowrun6e\Enums\SpellRange;
use Modules\Shadowrun6e\Enums\SpellType;
use Modules\Shadowrun6e\Models\Spell;
use Override;

/**
 * @mixin Spell
 */
class SpellResource extends JsonResource
{
    /**
     * @return array{
     *     category: SpellCategory,
     *     damage: array<int, DamageType>|null,
     *     description?: string,
     *     drain_value: int,
     *     duration: SpellDuration|int,
     *     id: string,
     *     indirect: bool,
     *     name: string,
     *     page: int,
     *     range: SpellRange,
     *     ruleset: string,
     *     type: SpellType,
     *     links: array{
     *         self: string,
     *         collection: string
     *     }
     * }
     */
    #[Override]
    public function toArray(Request $request): array
    {
        /** @var User $user */
        $user = $request->user();
        return [
            'category' => $this->category,
            'damage' => $this->damage,
            'description' => $this->when(
                $user->hasPermissionTo('view data'),
                $this->description,
            ),
            'drain_value' => $this->drain_value,
            'duration' => $this->duration,
            'id' => $this->id,
            'indirect' => $this->indirect,
            'name' => $this->name,
            'page' => $this->page,
            'range' => $this->range,
            'ruleset' => $this->ruleset,
            'type' => $this->type,
            'links' => [
                'self' => route('shadowrun6e.spells.show', $this->id),
                'collection' => route('shadowrun6e.spells.index'),
            ],
        ];
    }
}
