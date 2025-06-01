<?php

declare(strict_types=1);

namespace Modules\Subversion\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;
use Modules\Subversion\Models\Lineage;
use Modules\Subversion\Models\LineageOption;
use Override;

use function route;

/**
 * @mixin Lineage
 */
class LineageResource extends JsonResource
{
    /**
     * @return array{
     *   description: MissingValue|string,
     *   id: string,
     *   name: string,
     *   option: ?LineageOption,
     *   options: AnonymousResourceCollection,
     *   page: int,
     *   ruleset: string,
     *   links: array{
     *     self: string,
     *   },
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
            'options' => LineageOptionResource::collection(array_values($this->options)),
            'option' => $this->option,
            'page' => $this->page,
            'ruleset' => $this->ruleset,
            'links' => [
                'self' => route('subversion.lineages.show', $this->id),
            ],
        ];
    }
}
