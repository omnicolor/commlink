<?php

declare(strict_types=1);

namespace Modules\Battletech\Transformers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Battletech\Enums\QualityType;
use Modules\Battletech\Models\Quality;
use Override;

use function array_walk;

/**
 * @mixin Quality
 */
class TraitResource extends JsonResource
{
    /**
     * @return array{
     *     cost: int,
     *     description?: string,
     *     id: string,
     *     name: string,
     *     opposes: array<int, string>,
     *     quote: string,
     *     page: int,
     *     ruleset: string,
     *     types: array<int, QualityType>,
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
        $types = $this->types;
        array_walk($types, function (QualityType &$type): void {
            $type = $type->value;
        });
        return [
            'cost' => $this->cost,
            'description' => $this->when(
                $user->hasPermissionTo('view data'),
                $this->description,
            ),
            'id' => $this->id,
            'name' => $this->name,
            'opposes' => $this->opposes,
            'quote' => $this->quote,
            'page' => $this->page,
            'ruleset' => $this->ruleset,
            'types' => $types,
            'links' => [
                'self' => route('battletech.traits.show', $this->id),
            ],
        ];
    }
}
