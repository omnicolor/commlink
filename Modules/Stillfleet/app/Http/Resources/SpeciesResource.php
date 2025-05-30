<?php

declare(strict_types=1);

namespace Modules\Stillfleet\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Stillfleet\Models\Species;
use Override;

use function array_values;
use function count;
use function route;

/**
 * @mixin Species
 */
class SpeciesResource extends JsonResource
{
    /**
     * @return array{
     *     description?: string,
     *     id: string,
     *     languages: string,
     *     name: string,
     *     page: int,
     *     optional_powers: array<int, PowerResource>,
     *     powers: array<int, PowerResource>,
     *     powers_choose: int,
     *     powers_chosen?: array<int, PowerResource>,
     *     ruleset: string,
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
            'languages' => $this->languages,
            'name' => $this->name,
            'page' => $this->page,
            'ruleset' => $this->ruleset,
            'optional_powers' => array_values(
                (array)PowerResource::collection($this->optional_powers)
                    ->toArray($request),
            ),
            'powers' => array_values(
                (array)PowerResource::collection($this->powers)
                    ->toArray($request),
            ),
            'powers_choose' => $this->powers_choose,
            'powers_chosen' => $this->when(
                0 !== count($this->added_powers),
                PowerResource::collection($this->added_powers)
                    ->toArray($request),
            ),
            'links' => [
                'self' => route('stillfleet.species.show', $this->id),
            ],
        ];
    }
}
