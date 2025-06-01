<?php

declare(strict_types=1);

namespace Modules\Expanse\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Expanse\Models\Focus;
use Override;

use function route;

/**
 * @mixin Focus
 */
class FocusResource extends JsonResource
{
    /**
     * @return array{
     *     attribute: string,
     *     description?: string,
     *     level: int,
     *     name: string,
     *     page: int,
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
            'level' => $this->level,
            'name' => $this->name,
            'page' => $this->page,
            'links' => [
                'self' => route('expanse.focuses.show', $this->id),
            ],
        ];
    }
}
