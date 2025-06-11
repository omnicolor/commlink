<?php

declare(strict_types=1);

namespace Modules\Root\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;
use Modules\Root\Models\Nature;
use Override;

use function route;

/**
 * @mixin Nature
 */
class NatureResource extends JsonResource
{
    /**
     * @return array{
     *     description: MissingValue|string,
     *     id: string,
     *     name: string,
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
            'description' => $this->when(
                $user->hasPermissionTo('view data'),
                $this->description,
            ),
            'id' => $this->id,
            'name' => $this->name,
            'links' => [
                'self' => route('root.natures.show', $this->id),
            ],
        ];
    }
}
