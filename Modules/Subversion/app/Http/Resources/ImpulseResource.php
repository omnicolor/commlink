<?php

declare(strict_types=1);

namespace Modules\Subversion\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;
use Modules\Subversion\Models\Impulse;
use Override;

use function route;

/**
 * @mixin Impulse
 */
class ImpulseResource extends JsonResource
{
    /**
     * @return array{
     *   description: MissingValue|string,
     *   downtime: ImpulseDowntimeResource,
     *   id: string,
     *   name: string,
     *   page: int,
     *   responses: AnonymousResourceCollection,
     *   ruleset: string,
     *   links: array{
     *      self: string,
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
            'downtime' => new ImpulseDowntimeResource($this->downtime),
            'id' => $this->id,
            'name' => $this->name,
            'page' => $this->page,
            'responses' => ImpulseResponseResource::collection(array_values($this->responses)),
            'ruleset' => $this->ruleset,
            'links' => [
                'self' => route('subversion.impulses.show', $this->id),
            ],
        ];
    }
}
