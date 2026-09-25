<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Transformers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Shadowrun6e\Models\ComplexForm;
use Override;

/**
 * @mixin ComplexForm
 */
class ComplexFormResource extends JsonResource
{
    /**
     * @return array{
     *     description?: string,
     *     duration: string,
     *     fade_value: int|null,
     *     id: string,
     *     name: string,
     *     page: int,
     *     ruleset: string,
     *     links: array{
     *         self: string
     *     }
     * }
     */
    #[Override]
    public function toArray(Request $request): array
    {
        /** @var User $user */
        $user = $request->user();
        return [
            'description' => $this->when(
                $user->hasPermissionTo('view data'),
                $this->description,
            ),
            'duration' => $this->duration->value,
            'fade_value' => $this->fade_value,
            'id' => $this->id,
            'name' => $this->name,
            'page' => $this->page,
            'ruleset' => $this->ruleset,
            'links' => [
                'self' => route('shadowrun6e.complex-forms.show', $this->id),
                'collection' => route('shadowrun6e.complex-forms.index'),
            ],
        ];
    }
}
